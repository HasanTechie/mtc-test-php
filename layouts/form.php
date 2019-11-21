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