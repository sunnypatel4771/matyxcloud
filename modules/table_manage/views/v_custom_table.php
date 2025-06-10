<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();?>

<?php

$current_tab_data = [];

?>

<div id="wrapper" >

    <div class="content">

        <div class="row">

            <div class="col-md-3">

                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">

                    <?php echo _l('table_manage'); ?>

                </h4>

                <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">

                    <?php foreach ( $tabs as $tab ) {

                        if ( $tab['slug'] == $active_tab )

                            $current_tab_data = $tab;

                        ?>

                        <li class="<?php echo $tab['slug'] == $active_tab ? 'active' : ''?>">

                            <a href="<?php echo admin_url('table_manage/setting/custom_table?tab='.$tab['slug'] ); ?>"

                               data-group="">

                                <i class="<?php echo $tab['icon']?> menu-icon"></i>

                                <?php echo $tab['text']?>

                            </a>

                        </li>

                    <?php } ?>


                </ul>


            </div>



            <div class="col-md-9"  >

                <?php

               if ( !empty( $current_tab_data ) )
               {

                   $this->load->view('v_custom_table_content' , [ 'current_tab_data' => $current_tab_data ] );

               }
               else
               {

                   echo "Not found setting";

               }

               ?>

            </div>

        </div>



    </div>

</div>




<?php init_tail(); ?>


<script>


    (function($) {
        "use strict";

        $(document).ready(function (){


            $('#tm_custom_table_sortable').sortable();

            if ( $('#btn_table_manage_reset').length )
            {

                $('#btn_table_manage_reset').on('click',function (){

                    requestGetJSON( admin_url+"table_manage/setting/reset_custom_table/"+$('#table_hook').val()).done(function ( result ){

                        window.location.reload();

                    });

                })

            }


        });


    })(jQuery);


</script>

<style>

    #tm_custom_table_sortable{

        max-width: 600px;
    }

    #tm_custom_table_sortable .tm_custom_table_sortable_items{

        padding: 10px;
        margin: 5px 0;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 3px;
        cursor: move;

    }

    #tm_custom_table_sortable .tm_custom_table_sortable_items label{

        cursor: move;

    }

</style>


