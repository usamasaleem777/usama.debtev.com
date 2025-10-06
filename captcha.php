<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional CAPTCHA Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }
        
        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 16px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .captcha-btn {
            width: 100%;
            padding: 14px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .captcha-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .captcha-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalAppear 0.3s ease-out;
        }
        
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e1e8ed;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 20px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #7f8c8d;
            transition: color 0.3s;
        }
        
        .close-btn:hover {
            color: #e74c3c;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        /* CAPTCHA Styles */
        .captcha-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .captcha-type-btn {
            padding: 10px 15px;
            border: 2px solid #e1e8ed;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            flex: 1;
            min-width: 100px;
            text-align: center;
        }
        
        .captcha-type-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .captcha-type-btn:hover:not(.active) {
            border-color: #3498db;
            background: #f0f8ff;
        }
        
        .captcha-container {
            background: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .captcha-title {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .refresh-btn {
            padding: 6px 12px;
            background: #3498db;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            border: none;
            transition: background 0.3s;
        }
        
        .refresh-btn:hover {
            background: #2980b9;
        }
        
        /* Slider CAPTCHA */
        .slider-container {
            position: relative;
            margin-bottom: 15px;
        }
        
        .slider-track {
            position: relative;
            height: 50px;
            background: linear-gradient(90deg, #e1e8ed 0%, #3498db 100%);
            border-radius: 25px;
            overflow: hidden;
        }
        
        .slider-piece {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            cursor: grab;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            user-select: none;
            transition: transform 0.2s;
            z-index: 2;
        }
        
        .slider-piece:active {
            cursor: grabbing;
            transform: scale(1.1);
        }
        
        .slider-target {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            z-index: 1;
        }
        
        .slider-position {
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Math CAPTCHA */
        .math-challenge {
            text-align: center;
        }
        
        .math-question {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e1e8ed;
        }
        
        .math-input {
            width: 100%;
            padding: 12px;
            text-align: center;
            font-size: 18px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
        }
        
        /* Image Puzzle CAPTCHA */
        .puzzle-instruction {
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .puzzle-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .puzzle-item {
            aspect-ratio: 1;
            border: 3px solid #e1e8ed;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        
        .puzzle-item:hover {
            transform: scale(1.05);
        }
        
        .puzzle-item.selected {
            border-color: #3498db;
            background: #f0f8ff;
            transform: scale(1.05);
        }
        
        /* Text CAPTCHA */
        .text-challenge {
            text-align: center;
        }
        
        .captcha-image {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e1e8ed;
            background: white;
            padding: 10px;
        }
        
        .captcha-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Pattern CAPTCHA */
        .pattern-challenge {
            text-align: center;
        }
        
        .pattern-shapes {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
            font-size: 48px;
            flex-wrap: wrap;
        }
        
        .pattern-options {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .pattern-option {
            padding: 20px;
            font-size: 40px;
            border: 3px solid #e1e8ed;
            border-radius: 12px;
            cursor: pointer;
            background: white;
            transition: all 0.3s;
            min-width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pattern-option:hover {
            transform: scale(1.1);
            border-color: #3498db;
        }
        
        .pattern-option.selected {
            border-color: #3498db;
            background: #f0f8ff;
            transform: scale(1.1);
        }
        
        .verify-btn {
            width: 100%;
            padding: 14px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .verify-btn:hover:not(:disabled) {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .verify-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            display: none;
        }
        
        .message.success {
            background: #d4f8e8;
            color: #27ae60;
            border: 2px solid #a3e4c1;
        }
        
        .message.error {
            background: #fde8e8;
            color: #e74c3c;
            border: 2px solid #f5b7b1;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            
            .captcha-selector {
                gap: 5px;
            }
            
            .captcha-type-btn {
                padding: 8px 12px;
                font-size: 12px;
            }
            
            .pattern-option {
                padding: 15px;
                font-size: 30px;
                min-width: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact Us</h1>
        <p class="subtitle">We'd love to hear from you. Please fill out the form below.</p>
        
        <form id="contactForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email address">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required placeholder="Tell us about your inquiry..."></textarea>
            </div>
            
            <button type="button" class="captcha-btn" id="verifyCaptchaBtn">
                Verify CAPTCHA
            </button>
            
            <button type="submit" class="captcha-btn" id="submitBtn" style="display: none; background: #2ecc71;">
                Submit Form
            </button>
        </form>
        
        <div class="message" id="message"></div>
    </div>

    <!-- CAPTCHA Modal -->
    <div class="modal" id="captchaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Security Verification</h2>
                <button class="close-btn" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="captcha-selector">
                    <button class="captcha-type-btn active" data-type="slider">🎯 Slider</button>
                    <button class="captcha-type-btn" data-type="math">➕ Math</button>
                    <button class="captcha-type-btn" data-type="image_puzzle">🖼️ Puzzle</button>
                    <button class="captcha-type-btn" data-type="text">📝 Text</button>
                    <button class="captcha-type-btn" data-type="pattern">🔷 Pattern</button>
                </div>
                
                <div class="captcha-container">
                    <div class="captcha-title">
                        <span>Complete the Security Challenge</span>
                        <button type="button" class="refresh-btn" id="refreshCaptchaBtn">🔄 New Challenge</button>
                    </div>
                    <div id="captchaContent">
                        <div style="text-align: center; padding: 20px; color: #7f8c8d;">
                            Loading security challenge...
                        </div>
                    </div>
                </div>
                
                <button class="verify-btn" id="verifyChallengeBtn">
                    Verify Challenge
                </button>
                
                <div class="message" id="captchaMessage"></div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentCaptchaType = 'slider';
        let sliderPosition = 0;
        let selectedPuzzleItems = [];
        let selectedPattern = null;
        let isCaptchaVerified = false;
        
        // DOM elements
        const verifyCaptchaBtn = document.getElementById('verifyCaptchaBtn');
        const submitBtn = document.getElementById('submitBtn');
        const captchaModal = document.getElementById('captchaModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const refreshCaptchaBtn = document.getElementById('refreshCaptchaBtn');
        const verifyChallengeBtn = document.getElementById('verifyChallengeBtn');
        const captchaContent = document.getElementById('captchaContent');
        const messageDiv = document.getElementById('message');
        const captchaMessageDiv = document.getElementById('captchaMessage');
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // CAPTCHA type selection
            document.querySelectorAll('.captcha-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.captcha-type-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentCaptchaType = this.dataset.type;
                    loadCaptcha();
                });
            });
            
            // Open CAPTCHA modal
            verifyCaptchaBtn.addEventListener('click', function() {
                captchaModal.style.display = 'flex';
                loadCaptcha();
            });
            
            // Close CAPTCHA modal
            closeModalBtn.addEventListener('click', function() {
                captchaModal.style.display = 'none';
            });
            
            // Refresh CAPTCHA
            refreshCaptchaBtn.addEventListener('click', function() {
                loadCaptcha();
            });
            
            // Verify CAPTCHA challenge
            verifyChallengeBtn.addEventListener('click', function() {
                verifyCaptchaChallenge();
            });
            
            // Form submission
            document.getElementById('contactForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!isCaptchaVerified) {
                    showMessage('Please complete the CAPTCHA verification first.', 'error');
                    return;
                }
                
                submitForm();
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === captchaModal) {
                    captchaModal.style.display = 'none';
                }
            });
        });
        
        // Load CAPTCHA challenge
        async function loadCaptcha() {
            captchaContent.classList.add('loading');
            captchaMessageDiv.style.display = 'none';
            
            try {
                const formData = new FormData();
                formData.append('action', 'generate');
                formData.append('type', currentCaptchaType);
                
                const response = await fetch('captcha_backend.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const challenge = await response.json();
                renderCaptcha(challenge);
            } catch (error) {
                console.error('Error loading CAPTCHA:', error);
                captchaContent.innerHTML = `
                    <div style="text-align: center; color: #e74c3c; padding: 20px;">
                        Error loading security challenge. Please try again.
                    </div>
                `;
            } finally {
                captchaContent.classList.remove('loading');
            }
        }
        
        // Render CAPTCHA based on type
        function renderCaptcha(challenge) {
            selectedPuzzleItems = [];
            selectedPattern = null;
            sliderPosition = 0;
            
            switch (challenge.type) {
                case 'slider':
                    captchaContent.innerHTML = `
                        <div class="slider-container">
                            <div class="slider-track">
                                <div class="slider-piece" id="sliderPiece">👉</div>
                                <div class="slider-target">🎯</div>
                            </div>
                        </div>
                        <div class="slider-position" id="sliderPosition">Position: 0%</div>
                        <div style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 10px;">
                            Slide the piece to the target area
                        </div>
                    `;
                    initSlider();
                    break;
                    
                case 'math':
                    captchaContent.innerHTML = `
                        <div class="math-challenge">
                            <div class="math-question">${challenge.question}</div>
                            <input type="number" class="math-input" id="mathAnswer" placeholder="Enter your answer" required>
                        </div>
                    `;
                    break;
                    
                case 'image_puzzle':
                    let gridHTML = '<div class="puzzle-instruction">Select all <strong>' + challenge.target + '</strong> emojis</div>';
                    gridHTML += '<div class="puzzle-grid">';
                    challenge.grid.forEach((item, index) => {
                        gridHTML += `<div class="puzzle-item" data-index="${index}">${item}</div>`;
                    });
                    gridHTML += '</div>';
                    gridHTML += '<div style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 10px;">Click on all matching emojis</div>';
                    captchaContent.innerHTML = gridHTML;
                    initPuzzle();
                    break;
                    
                case 'text':
                    captchaContent.innerHTML = `
                        <div class="text-challenge">
                            <div class="captcha-image">
                                <img src="${challenge.image}" alt="CAPTCHA Code" style="width: 100%; height: auto;">
                            </div>
                            <input type="text" class="math-input" id="textAnswer" placeholder="Enter the code shown above" required style="text-transform: uppercase;">
                            <div style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 10px;">
                                Enter the characters exactly as shown
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'pattern':
                    captchaContent.innerHTML = `
                        <div class="pattern-challenge">
                            <div class="puzzle-instruction">Which shape completes the pattern?</div>
                            <div class="pattern-shapes">
                                ${challenge.shapes.slice(0, 3).join(' ')} <span style="color: #7f8c8d; font-size: 40px;">?</span>
                            </div>
                            <div class="pattern-options">
                                <div class="pattern-option" data-shape="⭐">⭐</div>
                                <div class="pattern-option" data-shape="▲">▲</div>
                                <div class="pattern-option" data-shape="●">●</div>
                                <div class="pattern-option" data-shape="■">■</div>
                                <div class="pattern-option" data-shape="♦">♦</div>
                            </div>
                        </div>
                    `;
                    initPattern();
                    break;
                    
                default:
                    captchaContent.innerHTML = `<div style="text-align: center; color: #7f8c8d;">Unknown challenge type</div>`;
            }
        }
        
        // Initialize slider
        function initSlider() {
            const piece = document.getElementById('sliderPiece');
            const positionDisplay = document.getElementById('sliderPosition');
            const track = piece.parentElement;
            let isDragging = false;
            
            const updatePosition = (x) => {
                const rect = track.getBoundingClientRect();
                x = Math.max(5, Math.min(x, rect.width - 45));
                piece.style.left = x + 'px';
                sliderPosition = Math.round((x / (rect.width - 50)) * 100);
                positionDisplay.textContent = `Position: ${sliderPosition}%`;
            };
            
            piece.addEventListener('mousedown', (e) => {
                isDragging = true;
                piece.style.cursor = 'grabbing';
            });
            
            document.addEventListener('mouseup', () => {
                isDragging = false;
                piece.style.cursor = 'grab';
            });
            
            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                const rect = track.getBoundingClientRect();
                const x = e.clientX - rect.left - 20;
                updatePosition(x);
            });
            
            // Touch support
            piece.addEventListener('touchstart', (e) => {
                isDragging = true;
                e.preventDefault();
            });
            
            document.addEventListener('touchend', () => {
                isDragging = false;
            });
            
            document.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const rect = track.getBoundingClientRect();
                const x = e.touches[0].clientX - rect.left - 20;
                updatePosition(x);
                e.preventDefault();
            });
        }
        
        // Initialize puzzle
        function initPuzzle() {
            document.querySelectorAll('.puzzle-item').forEach(item => {
                item.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        selectedPuzzleItems = selectedPuzzleItems.filter(i => i !== index);
                    } else {
                        this.classList.add('selected');
                        selectedPuzzleItems.push(index);
                    }
                });
            });
        }
        
        // Initialize pattern
        function initPattern() {
            document.querySelectorAll('.pattern-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.pattern-option').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedPattern = this.dataset.shape;
                });
            });
        }
        
        // Verify CAPTCHA challenge
        async function verifyCaptchaChallenge() {
            verifyChallengeBtn.disabled = true;
            captchaMessageDiv.style.display = 'none';
            
            // Get CAPTCHA response based on type
            let captchaResponse = '';
            
            switch (currentCaptchaType) {
                case 'slider':
                    captchaResponse = sliderPosition.toString();
                    break;
                case 'math':
                    captchaResponse = document.getElementById('mathAnswer').value.trim();
                    break;
                case 'image_puzzle':
                    captchaResponse = JSON.stringify(selectedPuzzleItems);
                    break;
                case 'text':
                    captchaResponse = document.getElementById('textAnswer').value.trim();
                    break;
                case 'pattern':
                    captchaResponse = selectedPattern;
                    break;
            }
            
            if (!captchaResponse || (currentCaptchaType === 'image_puzzle' && selectedPuzzleItems.length === 0) || (currentCaptchaType === 'pattern' && !selectedPattern)) {
                showCaptchaMessage('Please complete the security challenge.', 'error');
                verifyChallengeBtn.disabled = false;
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'verify');
                formData.append('type', currentCaptchaType);
                formData.append('response', captchaResponse);
                
                const response = await fetch('captcha_backend.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network error during verification');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showCaptchaMessage(result.message, 'success');
                    isCaptchaVerified = true;
                    
                    // Update UI
                    verifyCaptchaBtn.style.display = 'none';
                    submitBtn.style.display = 'block';
                    
                    // Close modal after 1.5 seconds
                    setTimeout(() => {
                        captchaModal.style.display = 'none';
                        showMessage('CAPTCHA verified successfully! You can now submit the form.', 'success');
                    }, 1500);
                } else {
                    showCaptchaMessage(result.message + (result.attempts_left ? ` (${result.attempts_left} attempts left)` : ''), 'error');
                    
                    if (result.attempts_left === 0) {
                        setTimeout(loadCaptcha, 1000);
                    }
                }
            } catch (error) {
                console.error('Verification error:', error);
                showCaptchaMessage('An error occurred during verification. Please try again.', 'error');
            }
            
            verifyChallengeBtn.disabled = false;
        }
        
        // Submit form
        async function submitForm() {
            submitBtn.disabled = true;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const description = document.getElementById('description').value.trim();
            
            try {
                const formData = new FormData();
                formData.append('action', 'submit_form');
                formData.append('name', name);
                formData.append('email', email);
                formData.append('description', description);
                
                const response = await fetch('captcha_backend.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network error during form submission');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    document.getElementById('contactForm').reset();
                    
                    // Reset CAPTCHA
                    isCaptchaVerified = false;
                    verifyCaptchaBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Submission error:', error);
                showMessage('An error occurred while submitting the form. Please try again.', 'error');
            }
            
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
        
        // Helper function to show messages
        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }
        
        // Helper function to show CAPTCHA messages
        function showCaptchaMessage(text, type) {
            captchaMessageDiv.textContent = text;
            captchaMessageDiv.className = `message ${type}`;
            captchaMessageDiv.style.display = 'block';
        }
    </script>
</body>
</html>