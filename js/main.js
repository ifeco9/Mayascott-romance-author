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
      var cartHTML = '<div class="cart-items">';
      var total = 0;

      cart.forEach(function (item, index) {
        var priceNum = parseFloat(item.price.replace(/[^0-9.]/g, '')) || 0;
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
    var index = parseInt(e.target.dataset.index);
    var action = e.target.dataset.action;
    if (action === 'increase') {
      cart[index].quantity += 1;
    } else if (action === 'decrease' && cart[index].quantity > 1) {
      cart[index].quantity -= 1;
    }
    saveCart();
    updateCartUI();
  }

  function handleRemoveItem(e) {
    var index = parseInt(e.target.dataset.index);
    cart.splice(index, 1);
    saveCart();
    updateCartUI();
  }

  function showAddedToCartFeedback(button) {
    var originalText = button.textContent;
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
  var announceSlides = document.querySelectorAll('.announcement-slide');
  var announcePrev = document.getElementById('announcePrev');
  var announceNext = document.getElementById('announceNext');
  var currentAnnounce = 0;
  var announceInterval;

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
  var heroSlides = document.querySelectorAll('.hero-slide');
  var heroDots = document.querySelectorAll('.hero-dot');
  var currentHero = 0;
  var heroInterval;

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
  var searchToggle = document.getElementById('searchToggle');
  var searchOverlay = document.getElementById('searchOverlay');
  var searchClose = document.getElementById('searchClose');

  if (searchToggle && searchOverlay) {
    searchToggle.addEventListener('click', function () {
      searchOverlay.classList.toggle('open');
      if (searchOverlay.classList.contains('open')) {
        setTimeout(function () {
          var input = searchOverlay.querySelector('input');
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
  var mobileBtn = document.getElementById('mobileMenuBtn');
  var mobileOverlay = document.getElementById('mobileNavOverlay');
  var mobileClose = document.getElementById('mobileNavClose');
  var mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');

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
  var testimonials = document.querySelectorAll('.testimonial');
  var testimonialDots = document.querySelectorAll('.testimonial-dot');
  var currentTestimonial = 0;
  var testimonialInterval;

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
  var forms = document.querySelectorAll('.newsletter-form, .footer-newsletter-form, .bonus-newsletter-form');
  forms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = this.querySelector('input[type="email"]');
      var email = input ? input.value.trim() : '';

      if (email && isValidEmail(email)) {
        var formData = new FormData(this);
        formData.append('form_type', 'newsletter');
        alert('Thank you for subscribing! Check your email to confirm.');
        if (input) input.value = '';
      } else {
        alert('Please enter a valid email address.');
      }
    });
  });

  // ===== CONTACT FORM VALIDATION =====
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();

      var name = document.getElementById('name').value.trim();
      var email = document.getElementById('email').value.trim();
      var subject = document.getElementById('subject').value.trim();
      var message = document.getElementById('message').value.trim();
      var formMessage = document.getElementById('formMessage');

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

      var formData = new FormData(this);
      formData.append('form_type', 'contact');

      showFormMessage(formMessage, 'Thank you for your message! We will get back to you soon.', 'success');
      this.reset();
    });
  }

  function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function showFormMessage(element, message, type) {
    element.textContent = message;
    element.className = 'form-message ' + type;
  }
});
