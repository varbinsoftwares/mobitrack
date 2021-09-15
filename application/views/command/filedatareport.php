<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
?>?>
<style>
    .product_text {
        float: left;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        width:350px
    }
    .product_title {
        font-weight: 700;
    }
    .price_tag{
        float: left;
        width: 100%;
        border: 1px solid #222d3233;
        margin: 2px;
        padding: 0px 2px;
    }
    .price_tag_final{
        width: 100%;
    }

    .exportdata{
        margin: 15px 0px 0px 0px;
    }
</style>
<!-- Main content -->


<?php

function userReportFunction($users, $headers) {
    ?>
    <table id="tableDataOrder" class="table table-bordered table-striped">
        <thead>
            <tr>
                <?php
                foreach ($headers as $key => $value) {
                    ?>
                    <th ><?php echo $value; ?></th>
                    <?php
                }
                ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($users)) {

                $count = 1;
                foreach ($users as $key => $value) {
                    ?>
                    <tr>
                        <?php
                        foreach ($headers as $key1 => $value1) {
                            ?>
                            <td><?php echo $value[$key1]; ?></td>
                            <?php
                        }
                        ?>




                        <td>
                            <a href="<?php echo site_url('Command/deleteFile/' . $value["id"]); ?>" class="btn btn-danger"><i class="fa fa-eye "></i> Delete</a>
                            <!--<a href="<?php echo site_url('Command/viewFile/' . $value["id"]); ?>" class="btn btn-danger"><i class="fa fa-eye "></i> View</a>-->

                        </td>
                    </tr>
                    <?php
                    $count++;
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>


<section class="content">
    <div class="">

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $page_title; ?>

                </h3>
                <div class="panel-tools">

                </div>

            </div>
            <div class="box-body">



                <!-- Tab panes -->
                <div class="row">


                    <div class="" style="padding:20px">
                        <?php userReportFunction($result_data, $headers); ?>
                    </div>
                </div>



            </div>
        </div>
    </div>
</section>
<!-- end col-6 -->
</div>


<?php
$this->load->view('layout/footer');
?> 
<script>
    $(function () {

        $('#tableDataOrder').DataTable({
            language: {
                "search": "Apply filter _INPUT_ to table"
            }
        })
    })

</script>