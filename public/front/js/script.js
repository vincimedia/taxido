/*=====================
    Header Sticky js
==========================*/
$(window).scroll(function () {
    if ($(this).scrollTop() > 120) {
        $('header').addClass('fixed');
    } else {
        $('header').removeClass('fixed');
    }
});


/*=====================
    home Contain opacity js
==========================*/
$(document).ready(function () {
    function updateStyles() {
        var scrollTop = $(window).scrollTop();
        var opacity = 1 - scrollTop / 200;

        $(".home-contain").css('opacity', opacity);

        if (opacity <= 0) {
            $(".home-contain").css('z-index', -1);
        } else {
            $(".home-contain").css('z-index', 0);
        }
    }

    // Update styles on page load
    updateStyles();

    // Update styles on scroll
    $(window).scroll(function () {
        updateStyles();
    });
});


/*=====================
    Counter js
==========================*/
if ("IntersectionObserver" in window) {
    let counterObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                let counter = entry.target;

                // Remove commas and parse the target number
                let target = parseInt(counter.innerText.replace(/,/g, ''));
                let step = target / 200;
                let current = 0;

                let timer = setInterval(function () {
                    current += step;

                    // Format the current number with commas
                    counter.innerText = Math.floor(current).toLocaleString();

                    if (Math.floor(current) >= target) {
                        clearInterval(timer);
                        counter.innerText = target.toLocaleString(); // Ensure the final number matches exactly
                    }
                }, 10);

                counterObserver.unobserve(counter);
            }
        });
    });

    let counters = document.querySelectorAll(".counter");
    counters.forEach(function (counter) {
        counterObserver.observe(counter);
    });
}

/*=====================
    sidebar js
==========================*/
const filterButton = document.querySelector(".navbar-toggler");
const filterSideBar = document.querySelector(".header-middle");
const filterOverlay = document.querySelector(".overlay");
const closeBtns = document.querySelectorAll(".close-menu"); // Select all close buttons

// Add class to the element
filterButton.addEventListener("click", function () {
    filterSideBar.classList.add("show");
    filterOverlay.classList.add("show");
});

// Loop through each close button and add event listener
closeBtns.forEach(function (closeBtn) {
    closeBtn.addEventListener("click", function () {
        filterSideBar.classList.remove("show");
        filterOverlay.classList.remove("show");
    });
});

filterOverlay.addEventListener("click", function () {
    filterSideBar.classList.remove("show");
    filterOverlay.classList.remove("show");
});


document.addEventListener("DOMContentLoaded", function () {
    var lastScrollTop = 0;
    var header = document.querySelector("header");
    var headerHeight = header.offsetHeight;

    window.addEventListener("scroll", function () {
        var windowTop = window.scrollY;

        if (windowTop >= headerHeight) {
            header.classList.add("nav-down");
        } else {
            header.classList.remove("nav-down");
            header.classList.remove("nav-up");
        }

        if (header.classList.contains("nav-down")) {
            if (windowTop < lastScrollTop) {
                header.classList.add("nav-up");
            } else {
                header.classList.remove("nav-up");
            }
        }

        lastScrollTop = windowTop;
        // document.getElementById("windowtop").textContent = "scrollTop: " + windowTop;
    });
});