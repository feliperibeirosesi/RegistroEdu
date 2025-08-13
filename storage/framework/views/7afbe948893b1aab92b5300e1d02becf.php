<script>
const profileIcon = document.getElementById("profile-icon");
const profileMenu = document.getElementById("profile-menu");

if (profileIcon && profileMenu) {
    profileIcon.addEventListener("click", () => {
        profileMenu.classList.toggle("show");
    });

    document.addEventListener("click", (event) => {
        if (!profileIcon.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.remove("show");
        }
    });
}
</script>
<?php /**PATH C:\Users\I2HM\Documents\GITHUB\RegistroEdu\resources\views/partials/scripts/profile-menu.blade.php ENDPATH**/ ?>