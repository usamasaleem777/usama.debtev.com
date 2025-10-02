<?php 
if (!empty($_SESSION) && isset($_SESSION['company_name'])){
$themeOptions = DB::Query("SELECT header_color,sidebar_color,sidebar_text_color FROM companies WHERE company_id='".$_SESSION['company_id']."'");
}else{

}
?>	
		<!-- META DATA -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="keywords" content="">

		<!-- FAVICON -->
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo SITE_ROOT;  ?>assets/images/brand/favicon.ico" />

		<title>Dashboard Panel for 
	<?php
	if (isset($_SESSION['company_name'])) { echo $_SESSION['company_name']; }
		
		?>
			
		</title>

		<!-- BOOTSTRAP CSS -->
		<link id="style" href="<?php echo SITE_ROOT;  ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

	
		
<!-- STYLE CSS -->
		<link href="<?php echo SITE_ROOT;  ?>assets/css/style.css" rel="stylesheet" />
		<link href="<?php echo SITE_ROOT;  ?>assets/css/dark-style.css" rel="stylesheet" />
		<link href="<?php echo SITE_ROOT;  ?>assets/css/transparent-style.css" rel="stylesheet">
		<link href="<?php echo SITE_ROOT;  ?>assets/css/skin-modes.css" rel="stylesheet" />

		<!--- FONT-ICONS CSS -->
<style>
:root {
	--sidebar-text-color:<?php echo $themeOptions[0]['sidebar_text_color']; ?>;
	--side-bar-color: <?php echo $themeOptions[0]['sidebar_color']; ?>;
    --primary-bg-color: <?php echo $themeOptions[0]['header_color']; ?>;
    --primary-bg-hover: #233ac5;
    --primary-bg-border: #6276ed;
    --dark-body: #292e4a;
    --dark-border: #30314e;
    --transparent-primary: #6c5ffc;
    --transparent-body: #362bb1;
    --transparent-theme: rgba(0, 0, 0, 0.2);
    --transparent-border: rgba(255, 255, 255, 0.1);
    --blue: #1a1a3c;
    --dark-primary-hover: #233ac5;
    --primary-transparentcolor: #eaedf7;
    --darkprimary-transparentcolor: #2b356e;
    --transparentprimary-transparentcolor: rgba(255, 255, 255, 0.05);
    --indigo: #4b0082;
    --purple: #6f42c1;
    --pink: #fc5296;
    --orange: #fd7e14;
    --yellow: #FBB034;
    --green: #28a745;
    --teal: #20c997;
    --cyan: #17a2b8;
    --white: #ffffff;
    --gray: #6c757d;
    --gray-dark: #343a40;
    --primary: #6c5ffc;
    --secondary: #6c757d;
    --success: #28a745;
    --info: #17a2b8;
    --warning: #ffc107;
    --danger: #dc3545;
    --light: #f8f9fa;
    --dark: #343a40;
    --breakpoint-xs: 0;
    --breakpoint-sm: 576px;
    --breakpoint-md: 768px;
    --breakpoint-lg: 992px;
    --breakpoint-xl: 1200px;
    --font-family-sans-serif: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
</style>
		<link href="<?php echo SITE_ROOT;  ?>assets/css/icons.css" rel="stylesheet" />

		<!-- COLOR SKIN CSS -->
		<link id="theme" rel="stylesheet" type="text/css" media="all" href="<?php echo SITE_ROOT;  ?>assets/colors/color1.css" />
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
		<script src="<?php echo SITE_ROOT;  ?>assets/js/jquery.min.js"></script>
		
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		
		<script src="https://liberty1financial.com/html2canvas.min.js" id="html2canvas.min" type="text/javascript"></script>
		
		<script>
/*jQuery.ajax({
				method:"post",
				url:"./ajax_helpers/get_theme_options.php",
				data:{opt:"get_theme_option"},
				success:function(data){
					var obj = jQuery.parseJSON(data);
					
					var root = document.querySelector(':root');
					var rootStyle = getComputedStyle(root);
					var primaryBgColor = rootStyle.getPropertyValue('--primary-bg-color');
					root.style.setProperty('--primary-bg-color',obj['primary_color']);
					root.style.setProperty('--side-bar-color',obj['sidebar_color']);
					root.style.setProperty('--sidebar-text-color',obj['sidebar_text_color']);
				}
			});*/
</script>