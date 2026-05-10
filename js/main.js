document.addEventListener('DOMContentLoaded', function () {

  // ===== CART DRAWER =====
  const cartToggle = document.getElementById('cartToggle');
  const cartOverlay = document.getElementById('cartOverlay');
  const cartDrawer = document.getElementById('cartDrawer');
  const cartClose = document.getElementById('cartClose');

  let cart = loadCart();

  function openCart() {
    if (!cartDrawer || !cartOverlay) return;
    cartDrawer.classList.add('open');
    cartOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeCart() {
    if (!cartDrawer || !cartOverlay) return;
    cartDrawer.classList.remove('open');
    cartOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (cartToggle) cartToggle.addEventListener('click', openCart);
  if (cartClose) cartClose.addEventListener('click', closeCart);
  if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

  function loadCart() {
    try {
      return JSON.parse(localStorage.getItem('maya_cart')) || [];
    } catch (e) {
      return [];
    }
  }

  function saveCart() {
    localStorage.setItem('maya_cart', JSON.stringify(cart));
  }

  // ===== ADD TO CART (event delegation) =====
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-add-to-cart]');
    if (!btn) return;
    e.preventDefault();

    const card = btn.closest('.product-card, .upcoming-card');
    if (!card) return;

    const id = btn.dataset.productId || '';
    const title = btn.dataset.productTitle || card.querySelector('h3')?.textContent?.trim() || 'Product';
    const price = btn.dataset.productPrice || '0.00';
    const image = btn.dataset.productImage || card.querySelector('img')?.src || '';

    const existingItem = cart.find(function (item) { return item.id === id; });
    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cart.push({ id: id, title: title, price: price, image: image, quantity: 1 });
    }

    saveCart();
    updateCartUI();
    showAddedToCartFeedback(btn);
    openCart();
  });

  function updateCartUI() {
    const cartBody = document.querySelector('.cart-drawer-body');
    if (!cartBody) return;

    if (cart.length === 0) {
      cartBody.innerHTML =
        '<div class="cart-empty">' +
          '<p>Your cart is empty</p>' +
          '<a href="index.html" class="btn btn-primary btn-sm" id="continueShopping">Continue shopping</a>' +
          '<div class="cart-login-prompt">' +
            '<p>Have an account?</p>' +
            '<a href="#">Log in</a> to check out faster.' +
          '</div>' +
        '</div>';
      document.getElementById('continueShopping')?.addEventListener('click', closeCart);
    } else {
      let cartHTML = '<div class="cart-items">';
      let total = 0;

      cart.forEach(function (item, index) {
        const priceNum = parseFloat(item.price.replace(/[^0-9.]/g, '')) || 0;
        total += priceNum * item.quantity;
        cartHTML +=
          '<div class="cart-item">' +
            '<img src="' + item.image + '" alt="' + item.title.replace(/"/g, '&quot;') + '" class="cart-item-image">' +
            '<div class="cart-item-details">' +
              '<h4>' + item.title + '</h4>' +
              '<p>$' + priceNum.toFixed(2) + '</p>' +
              '<div class="cart-item-quantity">' +
                '<button class="quantity-btn" data-index="' + index + '" data-action="decrease">-</button>' +
                '<span>' + item.quantity + '</span>' +
                '<button class="quantity-btn" data-index="' + index + '" data-action="increase">+</button>' +
              '</div>' +
            '</div>' +
            '<button class="cart-item-remove" data-index="' + index + '">&times;</button>' +
          '</div>';
      });

      cartHTML += '</div>';
      cartHTML +=
        '<div class="cart-footer">' +
          '<div class="cart-total">' +
            '<span>Total:</span>' +
            '<span>$' + total.toFixed(2) + '</span>' +
          '</div>' +
          '<a href="checkout.html" class="btn btn-primary btn-full">Checkout</a>' +
        '</div>';

      cartBody.innerHTML = cartHTML;

      document.querySelectorAll('.quantity-btn').forEach(function (btn) {
        btn.addEventListener('click', handleQuantityChange);
      });
      document.querySelectorAll('.cart-item-remove').forEach(function (btn) {
        btn.addEventListener('click', handleRemoveItem);
      });
    }
  }

  function handleQuantityChange(e) {
    const index = parseInt(e.target.dataset.index);
    const action = e.target.dataset.action;
    if (action === 'increase') {
      cart[index].quantity += 1;
    } else if (action === 'decrease' && cart[index].quantity > 1) {
      cart[index].quantity -= 1;
    }
    saveCart();
    updateCartUI();
  }

  function handleRemoveItem(e) {
    const index = parseInt(e.target.dataset.index);
    cart.splice(index, 1);
    saveCart();
    updateCartUI();
  }

  function showAddedToCartFeedback(button) {
    const originalText = button.textContent;
    button.textContent = 'Added!';
    button.style.background = '#2e7d32';
    setTimeout(function () {
      button.textContent = originalText;
      button.style.background = '';
    }, 1500);
  }

  // Initialize cart from localStorage on every page
  updateCartUI();

  // ===== ANNOUNCEMENT SLIDER =====
  const announceSlides = document.querySelectorAll('.announcement-slide');
  const announcePrev = document.getElementById('announcePrev');
  const announceNext = document.getElementById('announceNext');
  let currentAnnounce = 0;
  let announceInterval;

  if (announceSlides.length > 1) {
    function goToAnnounce(index) {
      announceSlides.forEach(function (s) { s.classList.remove('active'); });
      currentAnnounce = (index + announceSlides.length) % announceSlides.length;
      announceSlides[currentAnnounce].classList.add('active');
    }
    function nextAnnounce() { goToAnnounce(currentAnnounce + 1); }
    function prevAnnounce() { goToAnnounce(currentAnnounce - 1); }
    function startAnnounceAuto() { announceInterval = setInterval(nextAnnounce, 4000); }
    function resetAnnounceAuto() { clearInterval(announceInterval); startAnnounceAuto(); }

    if (announcePrev) announcePrev.addEventListener('click', function () { prevAnnounce(); resetAnnounceAuto(); });
    if (announceNext) announceNext.addEventListener('click', function () { nextAnnounce(); resetAnnounceAuto(); });
    startAnnounceAuto();
  }

  // ===== HERO SLIDER =====
  const heroSlides = document.querySelectorAll('.hero-slide');
  const heroDots = document.querySelectorAll('.hero-dot');
  let currentHero = 0;
  let heroInterval;

  if (heroSlides.length > 1) {
    function goToHero(index) {
      heroSlides.forEach(function (s) { s.classList.remove('active'); });
      heroDots.forEach(function (d) { d.classList.remove('active'); });
      currentHero = (index + heroSlides.length) % heroSlides.length;
      heroSlides[currentHero].classList.add('active');
      heroDots[currentHero].classList.add('active');
    }
    function nextHero() { goToHero(currentHero + 1); }
    function startHeroAuto() { heroInterval = setInterval(nextHero, 5000); }
    function resetHeroAuto() { clearInterval(heroInterval); startHeroAuto(); }

    heroDots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        goToHero(parseInt(this.getAttribute('data-slide')));
        resetHeroAuto();
      });
    });
    startHeroAuto();
  }

  // ===== SEARCH OVERLAY =====
  const searchToggle = document.getElementById('searchToggle');
  const searchOverlay = document.getElementById('searchOverlay');
  const searchClose = document.getElementById('searchClose');

  if (searchToggle && searchOverlay) {
    searchToggle.addEventListener('click', function () {
      searchOverlay.classList.toggle('open');
      if (searchOverlay.classList.contains('open')) {
        setTimeout(function () {
          const input = searchOverlay.querySelector('input');
          if (input) input.focus();
        }, 100);
      }
    });
    if (searchClose) {
      searchClose.addEventListener('click', function () {
        searchOverlay.classList.remove('open');
      });
    }
    document.addEventListener('click', function (e) {
      if (searchOverlay.classList.contains('open') && !searchOverlay.contains(e.target) && e.target !== searchToggle && !searchToggle.contains(e.target)) {
        searchOverlay.classList.remove('open');
      }
    });
  }

  // ===== MOBILE NAV =====
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileOverlay = document.getElementById('mobileNavOverlay');
  const mobileClose = document.getElementById('mobileNavClose');
  const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');

  if (mobileBtn && mobileOverlay) {
    mobileBtn.addEventListener('click', function () {
      mobileOverlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  }

  if (mobileClose && mobileOverlay) {
    mobileClose.addEventListener('click', function () {
      mobileOverlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }

  if (mobileOverlay) {
    mobileOverlay.addEventListener('click', function (e) {
      if (e.target === mobileOverlay) {
        mobileOverlay.classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  }

  mobileDropdownToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      this.parentElement.classList.toggle('open');
    });
  });

  // ===== TESTIMONIAL SLIDER =====
  const testimonials = document.querySelectorAll('.testimonial');
  const testimonialDots = document.querySelectorAll('.testimonial-dot');
  let currentTestimonial = 0;
  let testimonialInterval;

  if (testimonials.length > 1) {
    function goToTestimonial(index) {
      testimonials.forEach(function (t) { t.classList.remove('active'); });
      testimonialDots.forEach(function (d) { d.classList.remove('active'); });
      currentTestimonial = (index + testimonials.length) % testimonials.length;
      testimonials[currentTestimonial].classList.add('active');
      testimonialDots[currentTestimonial].classList.add('active');
    }
    function nextTestimonial() { goToTestimonial(currentTestimonial + 1); }
    function startTestAuto() { testimonialInterval = setInterval(nextTestimonial, 6000); }
    function resetTestAuto() { clearInterval(testimonialInterval); startTestAuto(); }

    testimonialDots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        goToTestimonial(parseInt(this.getAttribute('data-slide')));
        resetTestAuto();
      });
    });
    startTestAuto();
  }

  // ===== NEWSLETTER FORMS =====
  const forms = document.querySelectorAll('.newsletter-form, .footer-newsletter-form, .bonus-newsletter-form');
  forms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const input = this.querySelector('input[type="email"]');
      const email = input ? input.value.trim() : '';
      const submitBtn = this.querySelector('button[type="submit"]');

      if (!email || !isValidEmail(email)) {
        alert('Please enter a valid email address.');
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Subscribing...';
      }

      const formData = new FormData(this);
      fetch('subscribe.php', {
        method: 'POST',
        body: formData
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          alert(data.message);
          if (input) input.value = '';
        } else {
          alert(data.message || 'Something went wrong. Please try again.');
        }
      })
      .catch(function () {
        alert('Something went wrong. Please try again.');
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Subscribe';
        }
      });
    });
  });

  // ===== CONTACT FORM VALIDATION =====
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const subject = document.getElementById('subject').value.trim();
      const message = document.getElementById('message').value.trim();
      const formMessage = document.getElementById('formMessage');
      const submitBtn = this.querySelector('button[type="submit"]');

      formMessage.className = 'form-message';
      formMessage.textContent = '';

      if (!name) {
        showFormMessage(formMessage, 'Please enter your name.', 'error');
        return;
      }
      if (!email || !isValidEmail(email)) {
        showFormMessage(formMessage, 'Please enter a valid email address.', 'error');
        return;
      }
      if (!subject) {
        showFormMessage(formMessage, 'Please enter a subject.', 'error');
        return;
      }
      if (!message) {
        showFormMessage(formMessage, 'Please enter your message.', 'error');
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
      }

      const formData = new FormData(this);
      fetch('contact.php', {
        method: 'POST',
        body: formData
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          showFormMessage(formMessage, data.message, 'success');
          contactForm.reset();
        } else {
          showFormMessage(formMessage, data.message || 'Something went wrong.', 'error');
        }
      })
      .catch(function () {
        showFormMessage(formMessage, 'Something went wrong. Please try again.', 'error');
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      });
    });
  }

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function showFormMessage(element, message, type) {
    element.textContent = message;
    element.className = 'form-message ' + type;
  }

  // ===== BOOK CAROUSEL =====
  const carouselTrack = document.querySelector('.book-carousel-track');
  const carouselPrev = document.querySelector('.book-carousel-prev');
  const carouselNext = document.querySelector('.book-carousel-next');
  const carouselDots = document.getElementById('bookCarouselDots');

  if (carouselTrack && carouselPrev && carouselNext) {
    let carouselPos = 0;
    let slideWidth = 0;
    const slides = carouselTrack.children;
    const totalSlides = slides.length;

    function getSlideWidth() {
      if (slides.length > 0) {
        slideWidth = slides[0].offsetWidth;
      }
    }

    function createDots() {
      if (!carouselDots) return;
      carouselDots.innerHTML = '';
      for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement('button');
        dot.className = 'book-carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        dot.addEventListener('click', function () {
          carouselPos = -i * slideWidth;
          updateCarousel();
          updateDots(i);
        });
        carouselDots.appendChild(dot);
      }
    }

    function updateDots(activeIndex) {
      if (!carouselDots) return;
      Array.from(carouselDots.children).forEach(function (dot, i) {
        dot.classList.toggle('active', i === activeIndex);
      });
    }

    function updateCarousel() {
      getSlideWidth();
      const maxPos = -(totalSlides - 1) * slideWidth;
      carouselPos = Math.max(maxPos, Math.min(0, carouselPos));
      carouselTrack.style.transform = 'translateX(' + carouselPos + 'px)';
    }

    getSlideWidth();
    createDots();

    carouselNext.addEventListener('click', function () {
      const maxPos = -(totalSlides - 1) * slideWidth;
      if (carouselPos > maxPos) {
        carouselPos -= slideWidth;
        updateCarousel();
        var activeIndex = Math.round(Math.abs(carouselPos) / slideWidth);
        updateDots(activeIndex);
      }
    });

    carouselPrev.addEventListener('click', function () {
      if (carouselPos < 0) {
        carouselPos += slideWidth;
        updateCarousel();
        var activeIndex = Math.round(Math.abs(carouselPos) / slideWidth);
        updateDots(activeIndex);
      }
    });

    window.addEventListener('resize', function () {
      getSlideWidth();
      var clampedIndex = Math.round(Math.abs(carouselPos) / Math.max(slideWidth, 1));
      carouselPos = -clampedIndex * slideWidth;
      updateCarousel();
    });
  }

  // ===== SCROLL REVEAL =====
  const revealElements = document.querySelectorAll('.reveal');
  if (revealElements.length > 0 && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    revealElements.forEach(function (el) {
      revealObserver.observe(el);
    });
  }

  // ===== SPARKLE EFFECT =====
  const sparkleElements = document.querySelectorAll('.sparkle');
  sparkleElements.forEach(function (el) {
    el.style.animation = 'sparkle 2s ease-in-out infinite';
  });

  // ===== 3D BOOK TILT =====
  const book3D = document.getElementById('book3DInner');
  const bookContainer = document.getElementById('heroBook3D');
  if (book3D && bookContainer) {
    bookContainer.addEventListener('mousemove', function (e) {
      const rect = bookContainer.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = ((y - centerY) / centerY) * -12;
      const rotateY = ((x - centerX) / centerX) * 12;
      book3D.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale3d(1.02,1.02,1.02)';
    });
    bookContainer.addEventListener('mouseleave', function () {
      book3D.style.transform = 'rotateX(0deg) rotateY(0deg) scale3d(1,1,1)';
    });
  }
});
