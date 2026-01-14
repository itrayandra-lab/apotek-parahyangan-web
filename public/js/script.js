document.addEventListener("DOMContentLoaded", function () {
    // --- Hamburger Menu ---
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        // Close menu when a link is clicked
        document.querySelectorAll(".nav-link").forEach((link) => {
            link.addEventListener("click", () => {
                if (navMenu.classList.contains("active")) {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                }
            });
        });
    }

    // --- Hero Slider ---
    const slides = document.querySelectorAll(".hero__slide");
    const dots = document.querySelectorAll(".hero__dot");
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove("hero__slide--active");
            dots[i].classList.remove("hero__dot--active");
        });

        slides[index].classList.add("hero__slide--active");
        dots[index].classList.add("hero__dot--active");
        currentSlide = index;
    }

    function nextSlide() {
        const nextIndex = (currentSlide + 1) % slides.length;
        showSlide(nextIndex);
    }

    if (slides.length > 1) {
        // Auto-play the slider
        slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds

        // Add click event to dots
        dots.forEach((dot) => {
            dot.addEventListener("click", (e) => {
                const index = parseInt(e.target.getAttribute("data-index"));
                showSlide(index);
                // Reset interval on manual navigation
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            });
        });
    }
});
