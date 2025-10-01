<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<title><?php echo e($title ?? 'RegistroEdu'); ?></title>

<?php if(isset($css)): ?>
    <link rel="stylesheet" href="<?php echo e(asset("css/page/{$page}/{$css}.css")); ?>">
<?php endif; ?>

<?php if(isset($additionalCss)): ?>
    <?php $__currentLoopData = $additionalCss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cssFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link rel="stylesheet" href="<?php echo e(asset("css/{$cssFile}.css")); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\I2HM\Documents\GITHUB\RegistroEdu\resources\views/partials/head.blade.php ENDPATH**/ ?>