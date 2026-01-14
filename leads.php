
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
    
     <div class="main-content app-content"> 
        <div class="container-fluid">
             <!-- Page Header --> 
              <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
                 <div> 
                   
                    <h1 class="page-title font-medium text-lg mb-0">Leads</h1>
                </div> 
                <div class="btn-list">
                    <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"> <i class="ri-share-forward-line me-1"></i> Share </button>
                </div> 
            </div>  
            <div class="grid grid-cols-12 gap-6"> 
                <div class="xl:col-span-12 col-span-12"> 
                   <?php include_once "components/tables/leads.php" ?>
                </div> 
            </div> 
        </div> 
    </div>
    
      <?php include_once "components/layout/footer.php"; ?>