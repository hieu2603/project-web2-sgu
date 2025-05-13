<?php

$categories_stmt = $conn->prepare("SELECT * FROM categories");
$categories_stmt->execute();
$categories = $categories_stmt->get_result();
