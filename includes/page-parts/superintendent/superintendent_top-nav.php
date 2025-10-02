

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="index.php" class="navbar-brand">
        <img src="<?php
		if (isset($_SESSION['company_logo'])) {
			echo $_SESSION['company_logo'];
		}

		?>" alt="Company Logo" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"></span>
      </a>
 

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
 
          <li class="nav-item dropdown">
			  <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
				  <i class="fa fa-keyboard-o   "></i>&nbsp;Data Entry</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
            
			<li><a href="index.php?route=modules/dataentry/viewsalesintake" class="dropdown-item">View Sales Intake Forms</a></li>
	
            
              <li><a href="index.php?route=modules/dataentry/createsalesintake" class="dropdown-item">Create Sales Intake</a></li>
               
          
            </ul>
          </li>

              <!-- Level two dropdown
              <li class="dropdown-submenu dropdown-hover">
                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Hover for action</a>
                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                  <li>
                    <a tabindex="-1" href="#" class="dropdown-item">level 2</a>
                  </li>
				  -->
                  <!-- Level three dropdown
                  <li class="dropdown-submenu">
                    <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
                    <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                      <li><a href="#" class="dropdown-item">3rd level</a></li>
                      <li><a href="#" class="dropdown-item">3rd level</a></li>
                    </ul>
                  </li>
				  -->
                  <!-- End Level three -->
				  <!-- 
                  <li><a href="#" class="dropdown-item">level 2</a></li>
                  <li><a href="#" class="dropdown-item">level 2</a></li>
                </ul>
              </li>
			  -->
              <!-- End Level two -->
            </ul>
          </li>          
          
          
          
        </ul>

        <!-- SEARCH FORM -->
        <form method="GET" action="index.php" class="form-inline ml-0 ml-md-3">
          <div class="input-group input-group-sm">
            <input name="q" class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <input type="hidden" name="route"  value="modules/search/search" />
            <div class="input-group-append"> 
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
      
        <li class="nav-item">
          <a class="nav-link"  href="index.php?logout=1"  >
            <i class="fas fa-user   "></i> Logout <?php echo $_SESSION['name']; ?>
          </a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->
