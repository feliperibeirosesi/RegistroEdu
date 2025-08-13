<button class="profile-icon" id="profile-icon">
    <i class="fa-solid fa-circle-user"></i>
</button>

<div class="profile-menu" id="profile-menu" <?php echo e(isset($menuRole) ? "role={$menuRole}" : ''); ?> <?php echo e(isset($menuHidden) ? "aria-hidden={$menuHidden}" : ''); ?>>
    <?php
        $hasContent = false;
    ?>

    <?php if(isset($showLogout) && $showLogout && isset($logoutLink)): ?>
        <?php $hasContent = true; ?>
        <a href="<?php echo e($logoutLink['url']); ?>"><?php echo e($logoutLink['label']); ?></a>
    <?php endif; ?>

    <?php if(isset($customLinks) && count($customLinks) > 0): ?>
        <?php $hasContent = true; ?>
        <?php $__currentLoopData = $customLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($link['url']); ?>"><?php echo e($link['label']); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if(!empty(trim($slot ?? ''))): ?>
        <?php $hasContent = true; ?>
        <?php echo e($slot); ?>

    <?php endif; ?>

    <?php if (! ($hasContent)): ?>
        <p class="profile-menu-empty">Not found</p>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\I2HM\Documents\GITHUB\RegistroEdu\resources\views/partials/profile-menu.blade.php ENDPATH**/ ?>