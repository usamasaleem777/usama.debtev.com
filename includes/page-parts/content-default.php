<?php
switch ($_SESSION['role_id']) {
    case ROLE_ID_ADMIN:
        include_once('includes/page-parts/admins/admins_dashboard.php');
        $content_included = true;
        break;
	case ROLE_ID_MANAGER:
		include_once('includes/page-parts/manager/manager_dashboard.php');
		$content_included = true;
		break;	
	case ROLE_ID_CRAFTSMAN:
		include_once('includes/page-parts/craftsman/craftsman_dashboard.php');
		$content_included = true;
		break;
	case ROLE_ID_FOREMAN:
			include_once('includes/page-parts/forman/forman_dashboard.php');
			$content_included = true;
			break;
	case ROLE_ID_SUPERINTENDENT:
			include_once('includes/page-parts/superintendent/superintendent_dashboard.php');
			$content_included = true;
			break;
	case ROLE_ID_TOOL_MANAGER:
			include_once('includes/page-parts/tool_manager/tool_manager_dashboard.php');
			$content_included = true;
			break;
case ROLE_ID_HR:
			include_once('includes/page-parts/hr/hr_dashboard.php');
			$content_included = true;
			break;
    default:
        $content_included = false;
        break;
}
?>

<!-- Breadcrumb and common content -->
<?php if (!$content_included) : ?>
      <!--breadcrumb-->
	  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Components</div>
			<div class="ps-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0 p-0">
						<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Blank Page</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<div class="btn-group">
					<button type="button" class="btn btn-primary">Settings</button>
					<button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">Action</a>
						<a class="dropdown-item" href="javascript:;">Another action</a>
						<a class="dropdown-item" href="javascript:;">Something else here</a>
						<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;">Separated link</a>
					</div>
				</div>
			</div>
		</div>
		<!--end breadcrumb-->
      
	  <div class="card rounded-4" style="height:800px;">
	    <div class="card-body">
		    <h4>Where does it come from?</h4>
			<p class="mb-4">This page will never be visible. For each role, this will lead to dashboard of that role.</h4>
			<p class="mb-3">There are many variations of passages of Lorem Ipsum available, but the majority have suffered 
        alteration in some form, by injected humour, or randomised words which don't look even slightly believable. 
        If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden
         in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks 
         as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin 
         words, combined with a handful 
        of model sentence structures, to generate Lorem Ipsum which looks reasonable. 
        The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>

        <p class="mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
           Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a
           galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, 
           but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s
           with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker 
           including versions of Lorem Ipsum.</p>
		</div>
	  </div>

<?php endif; ?>