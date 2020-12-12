<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
</head>
<body>
<main>
    <section class="results_container">
        <span class="results_count">
            Showing  <?=   number_format($Employee_results_array->count);?> results
        </span>
        <?=   $Employee_results_array->list;?>
    </section>
</main>
</body>
</html>