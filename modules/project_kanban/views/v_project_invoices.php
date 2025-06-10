<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="panel_s panel-table-full">

    <div class="panel-body">

        <h4 class="tw-font-semibold tw-text-base tw-mb-4">

            <?php echo _l('project_invoices'); ?>

        </h4>

        <table class="table dt-table table-project-invoices" data-order-col="3" data-order-type="desc">

            <thead>

                <tr>

                    <th><?php echo _l('invoice_dt_table_heading_number'); ?></th>

                    <th><?php echo _l('invoice_dt_table_heading_amount'); ?></th>

                    <th><?php echo _l('invoice_total_tax'); ?></th>

                    <th><?php echo _l('invoice_dt_table_heading_date'); ?></th>

                    <th><?php echo _l('invoice_dt_table_heading_status'); ?></th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($invoices as $invoice ) { ?>

                <tr>

                    <td>
                        <?php
                        $invoice_number = format_invoice_number( $invoice->id );
                        $invoice_link = admin_url('invoices/list_invoices/'.$invoice->id);
                        ?>
                        <a class="btn" href="<?php echo $invoice_link?>" target="_blank">
                            <?php echo $invoice_number?>
                        </a>
                    </td>
                    <td><?php echo app_format_money( $invoice->total , $invoice->currency )?></td>
                    <td><?php echo app_format_money( $invoice->total_tax , $invoice->currency )?></td>
                    <td><?php echo _d( $invoice->date )?></td>
                    <td><?php echo format_invoice_status( $invoice->status )?></td>

                </tr>

                <?php

            } ?>

            </tbody>

        </table>

    </div>

</div>

