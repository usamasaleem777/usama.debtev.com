<?php
/**
 * Professional Self-Hosted CAPTCHA System
 * Enterprise-grade security with advanced bot detection
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ProfessionalCaptcha {
    private $challengeType;
    private $difficulty;
    private $sessionKey = 'professional_captcha';
    private $logFile;
    private $maxAttempts = 3;
    private $timeout = 300; // 5 minutes
    private $trustedProxies = []; // Configure your trusted proxies here
    
    // Rate limiting
    private $rateLimitKey = 'captcha_rate_limit';
    private $maxRequestsPerMinute = 10;
    
    const TYPE_MATH = 'math';
    const TYPE_IMAGE_PUZZLE = 'image_puzzle';
    const TYPE_SLIDER = 'slider';
    const TYPE_TEXT = 'text';
    const TYPE_PATTERN = 'pattern';
    
    public function __construct($challengeType = self::TYPE_SLIDER, $difficulty = 'medium') {
        $this->challengeType = $challengeType;
        $this->difficulty = $difficulty;
        $this->logFile = __DIR__ . '/security_logs.txt';
        
        // Ensure log directory exists
        if (!file_exists(dirname($this->logFile))) {
            @mkdir(dirname($this->logFile), 0755, true);
        }
        
        // ========================================
        // CONFIGURE YOUR PROXIES HERE (if needed)
        // ========================================
        
        // OPTION 1: No proxy (direct server - most common)
        // Just leave empty - the code will work automatically
        $this->trustedProxies = [];
        
        // OPTION 2: If using Cloudflare, uncomment these:
        /*
        $this->trustedProxies = [
            '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '104.16.0.0/13', '104.24.0.0/14', '108.162.192.0/18',
            '131.0.72.0/22', '141.101.64.0/18', '162.158.0.0/15',
            '172.64.0.0/13', '173.245.48.0/20', '188.114.96.0/20',
            '190.93.240.0/20', '197.234.240.0/22', '198.41.128.0/17'
        ];
        */
        
        // OPTION 3: If using your own proxy/load balancer, add its IP:
        // $this->trustedProxies = ['YOUR_PROXY_IP_HERE'];
        
        // OPTION 4: Multiple proxies or CIDR ranges:
        // $this->trustedProxies = ['10.0.0.5', '192.168.1.0/24'];
    }
    
    /**
     * Advanced Bot Detection
     */
    private function detectBot() {
        $suspicionScore = 0;
        $flags = [];
        
        // 1. Check User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($userAgent)) {
            $suspicionScore += 30;
            $flags[] = 'NO_USER_AGENT';
        } else {
            // Known bot patterns
            $botPatterns = [
                'bot', 'crawl', 'spider', 'scrape', 'curl', 'wget', 
                'python', 'java', 'perl', 'ruby', 'go-http', 'okhttp',
                'headless', 'phantom', 'selenium', 'puppeteer', 'playwright'
            ];
            
            foreach ($botPatterns as $pattern) {
                if (stripos($userAgent, $pattern) !== false) {
                    $suspicionScore += 40;
                    $flags[] = 'BOT_USER_AGENT';
                    break;
                }
            }
        }
        
        // 2. Check HTTP Headers (normal browsers send these)
        $requiredHeaders = ['HTTP_ACCEPT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING'];
        $missingHeaders = 0;
        foreach ($requiredHeaders as $header) {
            if (empty($_SERVER[$header])) {
                $missingHeaders++;
            }
        }
        if ($missingHeaders > 0) {
            $suspicionScore += ($missingHeaders * 10);
            $flags[] = 'MISSING_BROWSER_HEADERS';
        }
        
        // 3. Check for headless browser indicators
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request - normal, but track it
            $flags[] = 'AJAX_REQUEST';
        }
        
        // 4. Check Accept header
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (empty($accept) || strpos($accept, 'text/html') === false) {
            $suspicionScore += 15;
            $flags[] = 'INVALID_ACCEPT_HEADER';
        }
        
        // 5. Check for common automation tools
        $automationHeaders = [
            'HTTP_X_AUTOMATION_TOOL',
            'HTTP_X_MITMPROXY',
            'HTTP_X_FORWARDED_HOST',
            'HTTP_X_ORIGINAL_URL'
        ];
        foreach ($automationHeaders as $header) {
            if (isset($_SERVER[$header])) {
                $suspicionScore += 20;
                $flags[] = 'AUTOMATION_HEADERS_DETECTED';
                break;
            }
        }
        
        // 6. Check referrer (optional - some privacy tools block this)
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (empty($referer) && isset($_POST['action'])) {
            // POST request without referer is suspicious
            $suspicionScore += 10;
            $flags[] = 'NO_REFERER';
        }
        
        // 7. Rate limiting check
        if ($this->isRateLimited()) {
            $suspicionScore += 50;
            $flags[] = 'RATE_LIMIT_EXCEEDED';
        }
        
        // 8. Check for rapid requests (session-based timing)
        if (isset($_SESSION['last_captcha_request'])) {
            $timeSinceLastRequest = time() - $_SESSION['last_captcha_request'];
            if ($timeSinceLastRequest < 1) {
                $suspicionScore += 30;
                $flags[] = 'TOO_FAST_REQUEST';
            }
        }
        $_SESSION['last_captcha_request'] = time();
        
        // 9. JavaScript validation (check if JS token is present)
        if (isset($_POST['js_token'])) {
            $expectedToken = $_SESSION['js_validation_token'] ?? '';
            if ($_POST['js_token'] !== $expectedToken) {
                $suspicionScore += 40;
                $flags[] = 'INVALID_JS_TOKEN';
            }
        } else if (isset($_POST['action'])) {
            $suspicionScore += 25;
            $flags[] = 'NO_JS_TOKEN';
        }
        
        // 10. Check request timing (honeypot timing)
        if (isset($_SESSION['captcha_form_loaded_time']) && isset($_POST['action'])) {
            $timeSinceLoad = time() - $_SESSION['captcha_form_loaded_time'];
            if ($timeSinceLoad < 2) {
                // Submitted too fast (less than 2 seconds)
                $suspicionScore += 35;
                $flags[] = 'FORM_SUBMITTED_TOO_FAST';
            }
        }
        
        return [
            'is_bot' => $suspicionScore >= 50,
            'score' => $suspicionScore,
            'flags' => $flags,
            'ip' => $this->getClientIP()
        ];
    }
    
    /**
     * Rate Limiting
     */
    private function isRateLimited() {
        $ip = $this->getClientIP();
        $currentTime = time();
        
        if (!isset($_SESSION[$this->rateLimitKey])) {
            $_SESSION[$this->rateLimitKey] = [];
        }
        
        // Clean old entries
        $_SESSION[$this->rateLimitKey] = array_filter(
            $_SESSION[$this->rateLimitKey],
            function($timestamp) use ($currentTime) {
                return ($currentTime - $timestamp) < 60;
            }
        );
        
        // Count requests in last minute
        $requestCount = count($_SESSION[$this->rateLimitKey]);
        
        if ($requestCount >= $this->maxRequestsPerMinute) {
            $this->logSecurityEvent('RATE_LIMIT_EXCEEDED', [
                'ip' => $ip,
                'requests' => $requestCount
            ]);
            return true;
        }
        
        // Add current request
        $_SESSION[$this->rateLimitKey][] = $currentTime;
        
        return false;
    }
    
    /**
     * Get client IP address with CORRECT proxy support
     */
    private function getClientIP() {
        // Priority order: most reliable first
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare (only if using Cloudflare)
            'HTTP_X_REAL_IP',           // Nginx proxy
            'HTTP_X_FORWARDED_FOR',     // Standard proxy header
            'HTTP_CLIENT_IP',           // Rare, but some proxies use it
            'HTTP_X_FORWARDED',         // Less common
            'HTTP_X_CLUSTER_CLIENT_IP', // Specific load balancers
            'HTTP_FORWARDED_FOR',       // Legacy
            'HTTP_FORWARDED',           // RFC 7239
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                // Handle comma-separated IPs (X-Forwarded-For can contain multiple)
                $ipList = array_map('trim', explode(',', $_SERVER[$key]));
                
                // CRITICAL FIX: Get the LEFTMOST (first) IP in the chain
                // X-Forwarded-For format: "client_ip, proxy1_ip, proxy2_ip"
                // The leftmost is the ORIGINAL client IP
                foreach ($ipList as $ip) {
                    // Validate IP format first
                    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                        continue;
                    }
                    
                    // If we have trusted proxies configured
                    if (!empty($this->trustedProxies)) {
                        // Accept if it's a public IP (not private/reserved)
                        if ($this->isPublicIP($ip)) {
                            return $ip;
                        }
                    } else {
                        // No trusted proxies - only accept public IPs
                        if ($this->isPublicIP($ip)) {
                            return $ip;
                        }
                    }
                }
            }
        }

        // Fallback to REMOTE_ADDR (direct connection or last proxy in chain)
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $remoteAddr = trim($_SERVER['REMOTE_ADDR']);
            if (filter_var($remoteAddr, FILTER_VALIDATE_IP)) {
                return $remoteAddr;
            }
        }

        return '0.0.0.0'; // Unknown IP
    }

    /**
     * Check if IP is public (not private/reserved/loopback)
     */
    private function isPublicIP($ip) {
        // Validate IP first
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        
        // Check if it's a public IP (not private/reserved)
        // This excludes: 10.x.x.x, 172.16-31.x.x, 192.168.x.x, 127.x.x.x, etc.
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    /**
     * Check if IP is from trusted proxy
     */
    private function isTrustedProxy($ip, array $trustedProxies) {
        foreach ($trustedProxies as $proxy) {
            if ($this->ipInRange($ip, $proxy)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if IP is in CIDR range (supports IPv4)
     */
    private function ipInRange($ip, $range) {
        if (strpos($range, '/') === false) {
            return $ip === $range; // Exact match
        }

        // IPv4 CIDR check
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            list($subnet, $bits) = explode('/', $range);
            
            if (!filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return false;
            }
            
            $ip_long = ip2long($ip);
            $subnet_long = ip2long($subnet);
            $mask = -1 << (32 - (int)$bits);
            $subnet_long &= $mask;
            
            return ($ip_long & $mask) === $subnet_long;
        }
        
        // IPv6 CIDR check (basic implementation)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            list($subnet, $bits) = explode('/', $range);
            $bits = (int)$bits;
            
            $ip_bin = inet_pton($ip);
            $subnet_bin = inet_pton($subnet);
            
            if ($ip_bin === false || $subnet_bin === false) {
                return false;
            }
            
            $ip_bits = '';
            $subnet_bits = '';
            
            for ($i = 0; $i < strlen($ip_bin); $i++) {
                $ip_bits .= str_pad(decbin(ord($ip_bin[$i])), 8, '0', STR_PAD_LEFT);
                $subnet_bits .= str_pad(decbin(ord($subnet_bin[$i])), 8, '0', STR_PAD_LEFT);
            }
            
            return substr($ip_bits, 0, $bits) === substr($subnet_bits, 0, $bits);
        }
        
        return false;
    }
    
    /**
     * Generate a new CAPTCHA challenge
     */
    public function generateChallenge() {
        // Bot detection
        $botCheck = $this->detectBot();
        
        if ($botCheck['is_bot']) {
            $this->logSecurityEvent('BOT_DETECTED', $botCheck);
            
            return [
                'success' => false,
                'error' => 'Suspicious activity detected',
                'code' => 'BOT_DETECTED'
            ];
        }
        
        $challenge = [];
        
        switch ($this->challengeType) {
            case self::TYPE_MATH:
                $challenge = $this->generateMathChallenge();
                break;
            case self::TYPE_IMAGE_PUZZLE:
                $challenge = $this->generateImagePuzzle();
                break;
            case self::TYPE_SLIDER:
                $challenge = $this->generateSliderChallenge();
                break;
            case self::TYPE_TEXT:
                $challenge = $this->generateTextChallenge();
                break;
            case self::TYPE_PATTERN:
                $challenge = $this->generatePatternChallenge();
                break;
            default:
                $challenge = $this->generateSliderChallenge();
                break;
        }
        
        $challenge['timestamp'] = time();
        $challenge['attempts'] = 0;
        $challenge['ip'] = $this->getClientIP();
        $challenge['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $challenge['bot_score'] = $botCheck['score'];
        
        // Generate JS validation token
        $jsToken = bin2hex(random_bytes(16));
        $challenge['js_token'] = $jsToken;
        $_SESSION['js_validation_token'] = $jsToken;
        
        // Set form loaded time
        $_SESSION['captcha_form_loaded_time'] = time();
        
        $_SESSION[$this->sessionKey] = $challenge;
        
        $this->logSecurityEvent('CHALLENGE_GENERATED', [
            'type' => $this->challengeType,
            'ip' => $challenge['ip'],
            'bot_score' => $botCheck['score']
        ]);
        
        return $challenge;
    }
    
    /**
     * Generate math challenge
     */
    private function generateMathChallenge() {
        $operators = ['+', '-', '*'];
        $op = $operators[array_rand($operators)];
        $a = 0;
        $b = 0;
        $answer = 0;
        $question = '';
        
        switch ($op) {
            case '+':
                $a = rand(10, 50);
                $b = rand(10, 50);
                $answer = $a + $b;
                $question = "$a + $b = ?";
                break;
            case '-':
                $a = rand(20, 99);
                $b = rand(10, $a);
                $answer = $a - $b;
                $question = "$a - $b = ?";
                break;
            case '*':
                $a = rand(2, 12);
                $b = rand(2, 12);
                $answer = $a * $b;
                $question = "$a × $b = ?";
                break;
        }
        
        return [
            'type' => self::TYPE_MATH,
            'question' => $question,
            'answer' => (string)$answer
        ];
    }
    
    /**
     * Generate image puzzle (grid-based selection)
     */
    private function generateImagePuzzle() {
        $categories = [
            'animals' => ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨'],
            'vehicles' => ['🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒'],
            'food' => ['🍎', '🍌', '🍇', '🍓', '🍊', '🍉', '🍑', '🍒', '🍍']
        ];
        
        $category = array_rand($categories);
        $items = $categories[$category];
        shuffle($items);
        
        $target = $items[0];
        $grid = array_slice($items, 0, 9);
        
        $correctIndices = [];
        foreach ($grid as $index => $item) {
            if ($item === $target) {
                $correctIndices[] = $index;
            }
        }
        
        return [
            'type' => self::TYPE_IMAGE_PUZZLE,
            'category' => $category,
            'target' => $target,
            'grid' => $grid,
            'answer' => $correctIndices
        ];
    }
    
    /**
     * Generate slider challenge
     */
    private function generateSliderChallenge() {
        $targetPosition = rand(70, 90);
        $tolerance = 5;
        
        return [
            'type' => self::TYPE_SLIDER,
            'target' => $targetPosition,
            'tolerance' => $tolerance,
            'challenge_id' => bin2hex(random_bytes(16))
        ];
    }
    
    /**
     * Generate text challenge (distorted text)
     */
    private function generateTextChallenge() {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length = 6;
        $code = '';
        
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return [
            'type' => self::TYPE_TEXT,
            'code' => $code,
            'answer' => $code
        ];
    }
    
    /**
     * Generate pattern recognition challenge
     */
    private function generatePatternChallenge() {
        $patterns = [
            ['shapes' => ['⭐', '⭐', '⭐', '⭐'], 'answer' => '⭐'],
            ['shapes' => ['▲', '▲', '■', '▲'], 'answer' => '▲'],
            ['shapes' => ['●', '●', '●', '●'], 'answer' => '●'],
            ['shapes' => ['♦', '♦', '♦', '♦'], 'answer' => '♦']
        ];
        
        $pattern = $patterns[array_rand($patterns)];
        
        return [
            'type' => self::TYPE_PATTERN,
            'shapes' => $pattern['shapes'],
            'answer' => $pattern['answer']
        ];
    }
    
    /**
     * Verify the CAPTCHA response
     */
    public function verify($userResponse) {
        // Bot detection on verification
        $botCheck = $this->detectBot();
        
        if ($botCheck['is_bot']) {
            $this->logSecurityEvent('BOT_DETECTED_VERIFICATION', $botCheck);
            
            return [
                'success' => false,
                'message' => 'Suspicious activity detected'
            ];
        }
        
        if (!isset($_SESSION[$this->sessionKey])) {
            $this->logSecurityEvent('VERIFICATION_FAILED', [
                'reason' => 'No active challenge',
                'ip' => $this->getClientIP()
            ]);
            
            return [
                'success' => false,
                'message' => 'No active challenge found'
            ];
        }
        
        $challenge = $_SESSION[$this->sessionKey];
        
        // Check timeout
        if (time() - $challenge['timestamp'] > $this->timeout) {
            unset($_SESSION[$this->sessionKey]);
            
            $this->logSecurityEvent('VERIFICATION_FAILED', [
                'reason' => 'Challenge expired',
                'ip' => $this->getClientIP()
            ]);
            
            return [
                'success' => false,
                'message' => 'Challenge expired'
            ];
        }
        
        // Check max attempts
        $challenge['attempts']++;
        if ($challenge['attempts'] > $this->maxAttempts) {
            unset($_SESSION[$this->sessionKey]);
            
            $this->logSecurityEvent('VERIFICATION_FAILED', [
                'reason' => 'Max attempts exceeded',
                'ip' => $this->getClientIP(),
                'attempts' => $challenge['attempts']
            ]);
            
            return [
                'success' => false,
                'message' => 'Too many attempts'
            ];
        }
        
        $_SESSION[$this->sessionKey] = $challenge;
        
        // Verify based on type
        $verified = false;
        switch ($challenge['type']) {
            case self::TYPE_MATH:
                $verified = (string)$userResponse === (string)$challenge['answer'];
                break;
                
            case self::TYPE_IMAGE_PUZZLE:
                $userSelection = is_string($userResponse) ? json_decode($userResponse, true) : $userResponse;
                if (is_array($userSelection)) {
                    sort($userSelection);
                    $correctAnswer = $challenge['answer'];
                    sort($correctAnswer);
                    $verified = $userSelection === $correctAnswer;
                }
                break;
                
            case self::TYPE_SLIDER:
                $position = (int)$userResponse;
                $target = $challenge['target'];
                $tolerance = $challenge['tolerance'];
                $verified = abs($position - $target) <= $tolerance;
                break;
                
            case self::TYPE_TEXT:
                $verified = strtoupper(trim($userResponse)) === strtoupper(trim($challenge['answer']));
                break;
                
            case self::TYPE_PATTERN:
                $verified = $userResponse === $challenge['answer'];
                break;
        }
        
        if ($verified) {
            unset($_SESSION[$this->sessionKey]);
            
            $this->logSecurityEvent('VERIFICATION_SUCCESS', [
                'ip' => $this->getClientIP(),
                'type' => $challenge['type'],
                'bot_score' => $botCheck['score']
            ]);
            
            return [
                'success' => true,
                'message' => 'Verification successful'
            ];
        } else {
            $attemptsLeft = $this->maxAttempts - $challenge['attempts'];
            
            $this->logSecurityEvent('VERIFICATION_FAILED', [
                'reason' => 'Incorrect answer',
                'ip' => $this->getClientIP(),
                'type' => $challenge['type'],
                'attempts' => $challenge['attempts']
            ]);
            
            return [
                'success' => false,
                'message' => 'Incorrect answer. Please try again.',
                'attempts_left' => $attemptsLeft
            ];
        }
    }
    
    /**
     * Log security events
     */
    private function logSecurityEvent($event, $data = []) {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'data' => $data
        ];
        
        @file_put_contents($this->logFile, json_encode($log) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Generate SVG text CAPTCHA
     */
    public function generateTextSVG($text) {
        $width = 200;
        $height = 60;
        $chars = str_split($text);
        
        $svg = '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="100%" height="100%" fill="#f8f9fa"/>';
        
        // Add noise lines
        for ($i = 0; $i < 5; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $color = sprintf('#%02x%02x%02x', rand(200, 255), rand(200, 255), rand(200, 255));
            $svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="1"/>';
        }
        
        // Add text with distortion
        $x = 20;
        foreach ($chars as $char) {
            $y = rand(35, 45);
            $rotate = rand(-15, 15);
            $size = rand(24, 28);
            $color = sprintf('#%02x%02x%02x', rand(0, 100), rand(0, 100), rand(0, 100));
            
            $svg .= '<text x="' . $x . '" y="' . $y . '" font-size="' . $size . '" font-weight="bold" fill="' . $color . '" transform="rotate(' . $rotate . ' ' . $x . ' ' . $y . ')">' . htmlspecialchars($char) . '</text>';
            $x += 28;
        }
        
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Process form submission
     */
    public function processForm($data) {
        $name = filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $description = filter_var($data['description'] ?? '', FILTER_SANITIZE_STRING);
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($description)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email address'
            ];
        }
        
        $this->logSecurityEvent('FORM_SUBMITTED', [
            'ip' => $this->getClientIP(),
            'email' => $email,
            'name_length' => strlen($name)
        ]);
        
        return [
            'success' => true,
            'message' => 'Form submitted successfully! Thank you for your message.'
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    if ($action === 'generate') {
        $type = $_POST['type'] ?? ProfessionalCaptcha::TYPE_SLIDER;
        $captcha = new ProfessionalCaptcha($type);
        $challenge = $captcha->generateChallenge();
        
        // Check if bot was detected
        if (isset($challenge['error'])) {
            echo json_encode($challenge);
            exit;
        }
        
        if ($challenge['type'] === ProfessionalCaptcha::TYPE_TEXT) {
            $challenge['image'] = $captcha->generateTextSVG($challenge['code']);
        }
        
        // Remove sensitive data from response
        unset($challenge['answer']);
        
        echo json_encode($challenge);
        exit;
    }
    
    if ($action === 'verify') {
        $type = $_POST['type'] ?? ProfessionalCaptcha::TYPE_SLIDER;
        $response = $_POST['response'] ?? '';
        
        $captcha = new ProfessionalCaptcha($type);
        $result = $captcha->verify($response);
        
        echo json_encode($result);
        exit;
    }
    
    if ($action === 'submit_form') {
        $captcha = new ProfessionalCaptcha();
        $result = $captcha->processForm($_POST);
        
        echo json_encode($result);
        exit;
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
    exit;
}
?>