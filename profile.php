<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
 
<div class="main-content app-content">
     <div class="container-fluid"> 
        <!-- Page Header --> 
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2"> <div>
             <h1 class="page-title font-medium text-lg mb-0">Profile</h1>
        </div> 
        <div class="btn-list">
            <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> 
            <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"> <i class="ri-share-forward-line me-1"></i> Share </button> 
        </div> 
    </div> 
    <div class=""> 
        <div class="main-container-profile-card"> 
            <div class="box profile-card"> 
                <div class="profile-banner-img"> 
                    <img src="assets/images/media/media-3.jpg" class="card-img-top" alt="..."> </div> 
                    <div class="box-body pb-0 relative"> 
                        <div class="grid grid-cols-12 sm:gap-x-6 profile-content"> 
                            <div class="col-span-12"> 
                                <div class="box overflow-hidden border border-defaultborder dark:border-defaultborder/10"> 
                                    <div class="box-body border-b border-dashed border-defaultborder dark:border-defaultborder/10"> 
                                        <div class="text-center"> 
                                            <span class="avatar avatar-xxl avatar-rounded online mb-3"> 
                                                <img src="assets/images/faces/11.jpg" alt=""> 
                                            </span> 
                                            <h5 class="font-semibold mb-1">Spencer Robin</h5> 
                                            <span class="block font-medium text-textmuted dark:text-textmuted/50 mb-2">Software Development Manager</span> 
                                            <p class="text-xs mb-0 text-textmuted dark:text-textmuted/50">
                                                <span class="me-3">
                                                    <i class="ri-building-line me-1 align-middle"></i>Hamburg
                                                </span> 
                                                <span>
                                                    <i class="ri-map-pin-line me-1 align-middle"></i>Germany
                                                </span> 
                                            </p>
                                        </div> 
                                    </div>  
                                    <div class="p-4 pb-1 flex flex-wrap justify-between"> 
                                        <div class="font-medium text-[15px] text-primarytint1color"> Basic Info : </div> 
                                    </div> 
                                    <div class="box-body border-b border-dashed border-defaultborder dark:border-defaultborder/10 p-0"> 
                                        <ul class="ti-list-group list-group-flush !border-0"> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Name :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">Spencer Robin</span>
                                                </div> 
                                            </li> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Designation :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">Software Development Manager</span>
                                                </div> 
                                            </li> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Email :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">spencer. robin22@example.com</span>
                                                </div> 
                                            </li> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Phone :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">+1 (222) 111 - 57840</span>
                                                </div> 
                                            </li> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Experience :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">10 Years</span>
                                                </div> 
                                            </li> 
                                            <li class="ti-list-group-item pt-2 border-0"> 
                                                <div>
                                                    <span class="font-medium me-2">Age :</span>
                                                    <span class="text-textmuted dark:text-textmuted/50">28</span>
                                                </div> 
                                            </li> 
                                        </ul> 
                                    </div>   
                                </div> 
                            </div>  
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> <!-- End:: row-1 --> 
    </div> 
</div>

 <?php include_once "components/layout/footer.php"; ?>