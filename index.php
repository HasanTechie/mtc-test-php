<?php
require __DIR__ . '/vendor/autoload.php';
require 'basic.php';
require 'functions.php';

if (isset($_POST['submit'])) {
    if ($_POST['action'] == 'add') {
        addProperty();
    }
    if ($_POST['action'] == 'edit') {
        editProperty();
    }
}
if (!empty($_GET['delete'])) {
    deleteProperty();
}

if (!empty($_GET['edit'])) {
    $row = displayEditProperty();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MTC Trail Task 2019 | PHP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/cosmo/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <?php if (isset($_SESSION['message'])) { ?>
        <br/>
        <div class="alert alert-<?php echo $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <strong><?php echo $_SESSION['message'] ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        unset($_SESSION['message']);
    } ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <h1>Add/Edit Property</h1>
        <fieldset>
            <div class="form-group w-25">
                <label>County</label>
                <input type="text" name="county" class="form-control" placeholder="Enter county"
                       value="<?php echo(!empty($row['county']) ? $row['county'] : null); ?>" required>
            </div>
            <div class="form-group w-25">
                <label>Country</label>
                <input type="text" name="country" class="form-control" placeholder="Enter country"
                       value="<?php echo(!empty($row['country']) ? $row['country'] : null); ?>" required>
            </div>
            <div class="form-group w-25">
                <label>Town</label>
                <input type="text" name="town" class="form-control" placeholder="Enter Town"
                       value="<?php echo(!empty($row['town']) ? $row['town'] : null); ?>" required>
            </div>
            <div class="form-group w-50">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" rows="3"
                          placeholder="Enter Description" required><?php echo(!empty($row['description']) ? $row['description'] : null); ?></textarea>
            </div>
            <div class="form-group w-50">
                <label>Address</label>
                <input type="text" name="address" class="form-control" placeholder="Enter Address"
                       value="<?php echo(!empty($row['address']) ? $row['address'] : null); ?>" required>
            </div>
            <div class="form-group">
                <label for="exampleInputFile">Image File</label>
                <input type="file" class="form-control-file" name="image" id="image_full"
                       aria-describedby="fileHelp" required>
                <small id="fileHelp" class="form-text text-muted">Image will be converted to thumbnail.</small>
            </div>
            <div class="form-group w-25">
                <label for="num_bedrooms">Select Number of Bedrooms</label>
                <?php echo generateSelect('num_bedrooms', range(0, 12), (!empty($row['num_bedrooms']) ? $row['num_bedrooms'] : 0)); ?>
            </div>

            <div class="form-group w-25">
                <label for="num_bathrooms">Select Number of Bathrooms</label>
                <?php echo generateSelect('num_bathrooms', range(0, 12), (!empty($row['num_bathrooms']) ? $row['num_bathrooms'] : 0)); ?>
            </div>
            <div class="form-group w-25">
                <label>price</label>
                <input type="number" name="price" class="form-control" placeholder="Enter price"
                       value="<?php echo(!empty($row['price']) ? $row['price'] : null); ?>" required>
            </div>
            <div class="form-group w-25">
                <label for="property_type_id">Property type</label>
                <?php echo generateSelect('property_type_id', range(0, 7), (!empty($row['property_type_id']) ? $row['property_type_id'] : 0)); ?>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="type"
                           value="rent" <?php echo(empty($row['type']) ? 'checked=""' : (($row['type'] == 'rent') ? 'checked=""' : '')) ?>>
                    For Rent
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="type"
                           value="sale" <?php echo(empty($row['type']) ? '' : (($row['type'] == 'sale') ? 'checked=""' : '')) ?>>
                    For Sale
                </label>
            </div>
            <input type="hidden" name="action" value="<?php echo(!empty($row['id']) ? 'edit' : 'add'); ?>">
            <input type="hidden" name="id" value="<?php echo(!empty($row['id']) ? $row['id'] : null); ?>">
            <br/>
            <div class="form-group">
                <button type="submit" class="btn btn-<?php echo(!empty($row) ? 'primary' : 'success') ?>"
                        name="submit"><?php echo(!empty($row) ? 'Update Property' : 'Add Property') ?></button>
            </div>
        </fieldset>
    </form>
    <a href="load_data_from_api.php" onclick="return confirm('Are you sure?')">
        <button class="btn btn-secondary">Reload data from API</button>
    </a>
    <br/>
    <br/>
    <h1>Property List</h1>
    <table class="table table-hover">
        <thead>
        <tr>
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
        $query = "SELECT * FROM properties ORDER BY id DESC;";

        $result = mysqli_query($connection, $query)
        or die("Error at query " . $query . '-- ' . mysqli_errno($connection));
        if (mysqli_num_rows($result) >= 0) {
            $output = "";
            while ($row = mysqli_fetch_assoc($result)) {

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
        }
        ?>
        </tbody>
    </table>


</div>
</body>
</html>

