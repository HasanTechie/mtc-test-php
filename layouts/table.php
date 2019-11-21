<?php
$rows = $property->index();

if (is_array($rows)) {
    if (count($rows)) {
        ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination float-right">
                <?php if (count($rows) > 99) {
                    $property->pageLinks();
                } ?>
            </ul>
        </nav>
        <h1>Property List</h1>
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">County</th>
                <th scope="col">Country</th>
                <th scope="col">Town</th>
                <th scope="col">Description</th>
                <th scope="col">Address</th>
                <th scope="col">Image</th>
                <th scope="col">Thumbnail</th>
                <th scope="col">Bedrooms</th>
                <th scope="col">Bathrooms</th>
                <th scope="col">Price</th>
                <th scope="col" style="white-space: nowrap;">P. type</th>
                <th scope="col">Type</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $output = "";
            foreach ($rows as $row) {

                $actual_link = "http://$_SERVER[HTTP_HOST]/";

                if (filter_var($row['image_full'], FILTER_VALIDATE_URL)) {
                    $imageURL = "<a href='" . $row['image_full'] . "'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Image</button></a>";
                } else {
                    $imageURL = "<a href='" . $actual_link . "uploads/" . $row['image_full'] . "' target='_blank'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Image</button></a>";
                }

                if (filter_var($row['image_thumbnail'], FILTER_VALIDATE_URL)) {
                    $thumbnailImageURL = "<a href='" . $row['image_thumbnail'] . "'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Thumbnail</button></a>";
                } else {
                    $thumbnailImageURL = "<a href='" . $actual_link . "uploads/thumbs/" . $row['image_thumbnail'] . "' target='_blank'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Thumbnail</button></a>";
                }

                $output .=
                    "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['county'] . "</td>
                        <td>" . truncate($row['country'], 14) . "</td>
                        <td>" . $row['town'] . "</td>
                        <td>" . truncate($row['description'], 40) . "</td>
                        <td>" . $row['address'] . "</td>
                        <td>" . $imageURL . "</td>
                        <td>" . $thumbnailImageURL . "</td>
                        <td>" . $row['num_bedrooms'] . "</td>
                        <td>" . $row['num_bathrooms'] . "</td>
                        <td>" . $row['price'] . "</td>
                        <td>" . $row['property_type_id'] . "</td>
                        <td>" . $row['type'] . "</td>
                        <td style='white-space: nowrap;'><a href='$actual_link?edit=" . $row['id'] . "' class='btn btn-primary'>Edit</a> <a href='index.php?delete=" . $row['id'] . "' class='btn btn-danger'>Delete</a></td>
                    </tr>";
            }
            echo $output;
            ?>
            </tbody>
        </table>
        <?php
    }
}
?>