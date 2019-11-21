<?php
require __DIR__ . '/vendor/autoload.php';
require 'includes/basic.php';

spl_autoload_register(function ($className) {
    require_once 'includes/' . $className . '.php';
});

$property = new Property();

if (isset($_POST['submit'])) {
    if ($_POST['action'] == 'add') {
        $property->store($_POST);
    }
    if ($_POST['action'] == 'edit') {
        $property->update($_POST);
    }
}
if (!empty($_GET['delete'])) {
    $property->destroy($_GET['delete']);
}
if (!empty($_GET['edit'])) {
    $row = $property->edit($_GET['edit']);
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
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="/">MTC Media Trial Task 2019</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">Add Property <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a href="load_data_from_api.php" class="nav-link" onclick="return confirm('Are you sure?')"> Reload data
                    from API
                </a>
            </li>
        </ul>
    </div>
</nav>
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
        <h1 class="display-3"><?php echo(!empty($row) ? 'Edit' : 'Add') ?> Property</h1>
        <hr class="my-4">
        <fieldset>
            <div class="form-group w-25">
                <label>County</label>
                <input type="text" name="county" class="form-control" placeholder="Enter county"
                       value="<?php echo(!empty($row['county']) ? $row['county'] : null); ?>">
            </div>
            <div class="form-group w-25">
                <label>Country</label>
                <input type="text" name="country" class="form-control" placeholder="Enter country"
                       value="<?php echo(!empty($row['country']) ? $row['country'] : null); ?>">
            </div>
            <div class="form-group w-25">
                <label>Town</label>
                <input type="text" name="town" class="form-control" placeholder="Enter Town"
                       value="<?php echo(!empty($row['town']) ? $row['town'] : null); ?>">
            </div>
            <div class="form-group w-50">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" rows="3"
                          placeholder="Enter Description"><?php echo(!empty($row['description']) ? $row['description'] : null); ?></textarea>
            </div>
            <div class="form-group w-50">
                <label>Address</label>
                <input type="text" name="address" class="form-control" placeholder="Enter Address"
                       value="<?php echo(!empty($row['address']) ? $row['address'] : null); ?>">
            </div>
            <div class="form-group">

                <label for="exampleInputFile">Image File</label>
                <?php
                if (!empty($row['image_full'])) {

                    $actual_link = "http://$_SERVER[HTTP_HOST]/";

                    if (filter_var($row['image_full'], FILTER_VALIDATE_URL)) {
                        $imageURL = "<a href='" . $row['image_full'] . "'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Image</button></a>";
                    } else {
                        $imageURL = "<a href='" . $actual_link . "uploads/" . $row['image_full'] . "' target='_blank'><button style=\"white-space: nowrap;\" class='btn btn-primary'>View Image</button></a>";
                    }

                    echo '<span>' . $imageURL . '</span><br/><br/>';
                }
                ?>


                <input type="file" class="form-control-file" name="image" id="image_full"
                       aria-describedby="fileHelp">
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
                       value="<?php echo(!empty($row['price']) ? $row['price'] : null); ?>">
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
                <button type="submit" class="btn btn-success"
                        name="submit"><?php echo(!empty($row) ? 'Update' : 'Add') ?> Property
                </button>
            </div>
        </fieldset>
    </form>

    <br/>
    <br/>
    <?php
    $rows = $property->index();

    if (is_array($rows)) {
        if (count($rows)) {
            ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination float-right">
                    <?php
                    echo $property->pageLink(); ?>
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

</div>
</body>
</html>

