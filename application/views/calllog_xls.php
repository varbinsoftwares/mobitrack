
<table class="table">
    <tr>
        <th>Sn. No.</th>
        <th>Name</th>
        <th>Contact No.</th>
        <th>Call Type</th>
        <th>Duration (In minute)</th>
        <th>Date Time</th>
    </tr>
    <?php
    foreach ($contact as $key => $value) {
        ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo ucwords($value['name']); ?></td>
            <td><?php echo ucwords($value['contact_no']); ?></td>
            <td><?php echo str_replace("CallType.", "", $value['call_type']); ?></td>
            <td><?php echo $value['duration'] / 60; ?></td>
            <td><?php echo $value['date']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>