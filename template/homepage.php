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
        <?php if(isset($employee_count)): ?>
        <span class="results_count">
            Showing  <?=   number_format($employee_count);?> results
        </span>
        <?php endif;?>
       <?php if(isset($employee_list)):?>
        <?= $employee_list; ?>
       <?php else:?>
       <span class="alert">
           There was a problem with your request. Please try again later.
       </span>
        <?php endif;?>
    </section>
</main>
</body>
</html>