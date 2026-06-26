document.addEventListener('DOMContentLoaded', function () {

    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('show');
            }
        });
    }

    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlides.length > 1) {
        let current = 0;
        setInterval(() => {
            heroSlides[current].classList.remove('active');
            current = (current + 1) % heroSlides.length;
            heroSlides[current].classList.add('active');
        }, 5000);
    }

    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const suffix = counter.getAttribute('data-suffix') || '';
            let current = 0;
            const increment = Math.ceil(target / 80);
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target + suffix;
                    clearInterval(timer);
                } else {
                    counter.textContent = current + suffix;
                }
            }, 25);
        });
    }

    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        observer.observe(statsSection);
    }

    const forms = document.querySelectorAll('.booking-form, .contact-form form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (btn.getAttribute('data-loading') || 'Procesando...');

                const formData = new FormData(this);
                fetch(this.action, {
                    method: this.method || 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(() => { this.submit(); })
                .catch(() => { btn.disabled = false; btn.innerHTML = originalText; });
            }
        });
    });
});
