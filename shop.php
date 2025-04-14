<?php

// Fetch and display products
$sql = "SELECT id, name, description, price, image FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4 mb-4">';
        echo '  <div class="card">';
        if (!empty($row['image'])) {
            echo '    <img src="data:image/jpeg;base64,'.base64_encode($row['image']).'" class="card-img-top" alt="'.$row['name'].'" style="height: 200px; object-fit: cover;">';
        } else {
            echo '    <img src="path/to/default/image.jpg" class="card-img-top" alt="Default Image" style="height: 200px; object-fit: cover;">';
        }
        echo '    <div class="card-body">';
        echo '      <h5 class="card-title">'.$row['name'].'</h5>';
        echo '      <p class="card-text">'.$row['description'].'</p>';
        echo '      <p class="card-text"><strong>Price: $'.$row['price'].'</strong></p>';
        echo '      <a href="product.php?id='.$row['id'].'" class="btn btn-primary">View Details</a>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-12"><p>No products found.</p></div>';
} 