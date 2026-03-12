/* ============================================
   Dra. Samla Cristie - JavaScript
   Mobile menu, FAQ accordion, scroll effects
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ---- Mobile Menu Toggle ----
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');

    mobileToggle.addEventListener('click', () => {
        mobileToggle.classList.toggle('active');
        navLinks.classList.toggle('open');
        document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
    });

    // Close menu on link click
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileToggle.classList.remove('active');
            navLinks.classList.remove('open');
            document.body.style.overflow = '';
        });
    });

    // ---- Navbar scroll effect ----
    const navbar = document.getElementById('navbar');

    const handleScroll = () => {
        if (window.scrollY > 60) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // ---- FAQ Accordion ----
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            const isActive = item.classList.contains('active');

            // Close all items in the same category
            const category = item.closest('.faq-category');
            category.querySelectorAll('.faq-item').forEach(i => {
                i.classList.remove('active');
            });

            // Toggle clicked item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // ---- Scroll Reveal Animation ----
    const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // ---- Contact Form (redirect to WhatsApp) ----
    const form = document.getElementById('contatoForm');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const nome = document.getElementById('nome').value.trim();
        const telefone = document.getElementById('telefone').value.trim();
        const animal = document.getElementById('animal');
        const animalText = animal.options[animal.selectedIndex]?.text || '';
        const mensagem = document.getElementById('mensagem').value.trim();

        const text = `Olá Samy, vim do site! 🐾\n\n` +
                     `*Nome:* ${nome}\n` +
                     `*Telefone:* ${telefone}\n` +
                     `*Animal:* ${animalText}\n` +
                     `*Mensagem:* ${mensagem}`;

        const waUrl = `https://wa.me/5562994793553?text=${encodeURIComponent(text)}`;
        window.open(waUrl, '_blank');
    });

    // ---- Smooth scroll for anchor links ----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const offset = navbar.offsetHeight + 10;
                const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

    // ---- Active nav link highlight ----
    const sections = document.querySelectorAll('section[id]');

    const highlightNav = () => {
        const scrollY = window.scrollY + navbar.offsetHeight + 60;

        sections.forEach(section => {
            const top = section.offsetTop;
            const height = section.offsetHeight;
            const id = section.getAttribute('id');
            const link = document.querySelector(`.nav-links a[href="#${id}"]`);

            if (link) {
                if (scrollY >= top && scrollY < top + height) {
                    link.style.background = 'rgba(255,255,255,0.15)';
                } else {
                    link.style.background = 'transparent';
                }
            }
        });
    };

    window.addEventListener('scroll', highlightNav, { passive: true });

});
