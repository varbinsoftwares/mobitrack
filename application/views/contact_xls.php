
<table class="table">
    <tr>
        <th>Sn. No.</th>
        <th>Name</th>
        <th>Contact No.</th>
        <th>Update Date</th>
        <th>Update Time</th>
    </tr>
    <?php
    foreach ($contact as $key => $value) {
        ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo ucwords($value['name']); ?></td>
            <td><?php echo ucwords($value['contact_no']); ?></td>
            <td><?php echo $value['date']; ?></td>
            <td><?php echo $value['time']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>