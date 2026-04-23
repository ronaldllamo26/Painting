document.addEventListener('DOMContentLoaded', () => {
    const galleryContainer = document.getElementById('galleryContainer');
    const tagFilters = document.getElementById('tagFilters');
    const paymentMethod = document.getElementById('paymentMethod');
    const gcashSection = document.getElementById('gcashSection');
    const checkoutForm = document.getElementById('checkoutForm');
    const navbarSearch = document.getElementById('navbarSearch');
    const trackForm = document.getElementById('trackForm');
    const trackResult = document.getElementById('trackResult');
    const orderList = document.getElementById('orderList');
    const verifyForm = document.getElementById('verifyForm');
    const verifyResult = document.getElementById('verifyResult');
    
    let allArtworks = [];
    let filteredArtworks = [];
    let currentPage = 1;
    const itemsPerPage = 6;

    // Security: XSS Sanitization
    const sanitizeHTML = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    // Fetch Artworks
    async function fetchArtworks() {
        try {
            const response = await fetch('api/artworks.php');
            const result = await response.json();
            
            if (result.status === 'success') {
                allArtworks = result.data;
                filteredArtworks = [...allArtworks];
                renderGallery(filteredArtworks);
                renderFilters(result.tags);
            }
        } catch (error) {
            console.error('Error fetching artworks:', error);
            galleryContainer.innerHTML = '<p class="text-danger">Failed to load gallery.</p>';
        }
    }

    // Render Gallery with Pagination
    function renderGallery(artworks) {
        galleryContainer.innerHTML = '';
        
        const totalItems = artworks.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        if (totalItems === 0) {
            galleryContainer.innerHTML = '<div class="col-12 text-center text-secondary py-5">No artworks found.</div>';
            document.getElementById('paginationContainer').innerHTML = '';
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedItems = artworks.slice(startIndex, endIndex);

        paginatedItems.forEach(art => {
            const isSold = art.status === 'Sold';
            const isPending = art.status === 'Pending';
            const statusBadge = isSold ? '<div class="sold-overlay">SOLD</div>' : (isPending ? '<div class="pending-overlay">PENDING</div>' : '');
            
            const card = document.createElement('div');
            card.className = 'col-lg-4 col-md-6 artwork-item animate__animated animate__fadeIn';
            
            card.innerHTML = `
                <div class="artwork-card glass-card h-100">
                    <div class="artwork-img-container" onclick="viewDetails(${art.id})">
                        <img src="${art.image_url}" class="artwork-img" alt="${art.title}" loading="lazy">
                        ${statusBadge}
                    </div>
                    <div class="artwork-info text-center">
                        <h5 class="artwork-title fw-bold" style="font-family: var(--font-serif);">${sanitizeHTML(art.title)}</h5>
                        <p class="text-secondary x-small mb-2 text-uppercase" style="letter-spacing: 2px;">${art.size} | ${art.medium}</p>
                        <p class="artwork-price fw-bold mb-3">₱${parseFloat(art.price).toLocaleString()}</p>
                        <button class="btn btn-dark w-100 rounded-0 py-2 small fw-bold" onclick="viewDetails(${art.id})">BUY NOW</button>
                    </div>
                </div>
            `;
            galleryContainer.appendChild(card);
        });
        
        renderPagination(totalPages);
    }

    // Render Pagination
    function renderPagination(totalPages) {
        const container = document.getElementById('paginationContainer');
        container.innerHTML = '';
        if (totalPages <= 1) return;

        const ul = document.createElement('ul');
        ul.className = 'pagination gap-2';

        // Previous Arrow
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link shadow-sm" href="javascript:void(0)"><i class="fas fa-chevron-left"></i></a>`;
        if (currentPage > 1) {
            prevLi.onclick = () => { currentPage--; renderGallery(filteredArtworks); scrollUp(); };
        }
        ul.appendChild(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link shadow-sm" href="javascript:void(0)">${i}</a>`;
            li.onclick = () => { currentPage = i; renderGallery(filteredArtworks); scrollUp(); };
            ul.appendChild(li);
        }

        // Next Arrow
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link shadow-sm" href="javascript:void(0)"><i class="fas fa-chevron-right"></i></a>`;
        if (currentPage < totalPages) {
            nextLi.onclick = () => { currentPage++; renderGallery(filteredArtworks); scrollUp(); };
        }
        ul.appendChild(nextLi);

        container.appendChild(ul);
    }

    function scrollUp() {
        window.scrollTo({top: document.getElementById('gallery').offsetTop - 100, behavior: 'smooth'});
    }

    // Render Filters
    function renderFilters(tags) {
        tagFilters.innerHTML = '<button class="filter-btn active" data-filter="all">All Pieces</button>';
        const allBtn = tagFilters.querySelector('[data-filter="all"]');
        allBtn.onclick = () => filterByTag('all', allBtn);

        tags.forEach(tag => {
            const btn = document.createElement('button');
            btn.className = 'filter-btn';
            btn.textContent = tag;
            btn.onclick = () => filterByTag(tag, btn);
            tagFilters.appendChild(btn);
        });
    }

    window.filterByTag = (tag, btn) => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1;

        if (tag === 'all') {
            filteredArtworks = [...allArtworks];
        } else {
            filteredArtworks = allArtworks.filter(art => {
                if (!art.ai_tags) return false;
                return art.ai_tags.toLowerCase().split(',').map(t => t.trim()).includes(tag.toLowerCase());
            });
        }
        renderGallery(filteredArtworks);
    };

    // View Details & Sharing
    window.viewDetails = (id) => {
        const art = allArtworks.find(a => a.id == id);
        if (!art) return;

        document.getElementById('modalArtTitle').textContent = art.title;
        document.getElementById('modalArtImg').src = art.image_url;
        document.getElementById('modalArtPrice').textContent = `₱${parseFloat(art.price).toLocaleString()}`;
        document.getElementById('modalArtDetails').textContent = `${art.size} | ${art.medium}`;
        document.getElementById('modalArtDesc').textContent = art.ai_description;
        document.getElementById('checkoutArtId').value = art.id;

        // Social Share Links
        const shareUrl = encodeURIComponent(window.location.origin + window.location.pathname + '?art=' + id);
        document.getElementById('shareFB').href = `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`;
        document.getElementById('shareMessenger').href = `fb-messenger://share/?link=${shareUrl}`;

        checkoutForm.reset();
        paymentMethod.dispatchEvent(new Event('change'));
        
        if (art.status !== 'Available') {
            document.getElementById('btnPlaceOrder').style.display = 'none';
        } else {
            document.getElementById('btnPlaceOrder').style.display = 'block';
        }

        new bootstrap.Modal(document.getElementById('buyModal')).show();
    };

    window.copyLink = () => {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({ icon: 'success', title: 'Link Copied', timer: 1500, showConfirmButton: false });
        });
    };

    paymentMethod.addEventListener('change', (e) => {
        if (e.target.value === 'GCash') {
            gcashSection.classList.remove('d-none');
        } else {
            gcashSection.classList.add('d-none');
        }
    });

    // Cart Logic
    window.cart = JSON.parse(localStorage.getItem('art_cart')) || [];

    window.openCart = () => {
        const list = document.getElementById('cartItemsList');
        const empty = document.getElementById('cartEmptyMsg');
        list.innerHTML = '';
        
        if (window.cart.length === 0) {
            empty.classList.remove('d-none');
        } else {
            empty.classList.add('d-none');
            window.cart.forEach((item, index) => {
                list.innerHTML += `
                    <div class="d-flex align-items-center mb-3 p-2 border rounded">
                        <img src="${item.image_url}" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small fw-bold">${sanitizeHTML(item.title)}</h6>
                            <p class="mb-0 x-small text-secondary">₱${parseFloat(item.price).toLocaleString()}</p>
                        </div>
                        <button class="btn btn-sm text-danger" onclick="removeFromCart(${index})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
        }
        new bootstrap.Modal(document.getElementById('cartModal')).show();
    };

    window.addToCartById = (id) => {
        const art = allArtworks.find(a => a.id == id);
        if (!art) return;
        if (window.cart.some(item => item.id === art.id)) {
            Swal.fire('Info', 'Already in your bag.', 'info');
            return;
        }
        window.cart.push(art);
        localStorage.setItem('art_cart', JSON.stringify(window.cart));
        window.updateCartUI();
        Swal.fire({ icon: 'success', title: 'Added to Bag', timer: 1000, showConfirmButton: false });
    };

    window.removeFromCart = (index) => {
        window.cart.splice(index, 1);
        localStorage.setItem('art_cart', JSON.stringify(window.cart));
        window.updateCartUI();
        window.openCart(); // Refresh modal
    };

    window.updateCartUI = () => {
        const counts = document.querySelectorAll('.cart-count');
        counts.forEach(el => el.textContent = window.cart.length);
    };

    // Commission Form
    const commissionForm = document.getElementById('commissionForm');
    if (commissionForm) {
        commissionForm.onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(commissionForm);
            try {
                const response = await fetch('api/request_commission.php', { 
                    method: 'POST', 
                    body: formData 
                });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire('Sent!', result.message, 'success').then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('commissionModal')).hide();
                        commissionForm.reset();
                    });
                } else { Swal.fire('Error', result.message, 'error'); }
            } catch (err) { Swal.fire('Error', 'Failed to send request.', 'error'); }
        };
    }

    // Forms Logic
    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(checkoutForm);
        try {
            const response = await fetch('api/place_order.php', { 
                method: 'POST', 
                body: formData 
            });
            const result = await response.json();
            if (result.status === 'success') {
                Swal.fire('Success!', 'Order placed successfully.', 'success').then(() => location.reload());
            } else { Swal.fire('Error', result.message, 'error'); }
        } catch (err) { Swal.fire('Error', 'Submission failed.', 'error'); }
    });

    trackForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const contact = document.getElementById('trackContact').value;
        try {
            const response = await fetch('api/track_order.php', { 
                method: 'POST', 
                body: new FormData(trackForm)
            });
            const result = await response.json();
            if (result.status === 'success') {
                trackResult.classList.remove('d-none');
                orderList.innerHTML = result.data.map(o => `
                    <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                        <img src="${o.art_img}" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 rounded">
                        <div class="flex-grow-1"><h6 class="mb-0 small fw-bold">${sanitizeHTML(o.art_title)}</h6><p class="mb-0 x-small text-secondary">${o.order_status}</p></div>
                    </div>`).join('');
            } else { Swal.fire('Error', result.message, 'error'); }
        } catch (err) { Swal.fire('Error', 'Tracking failed.', 'error'); }
    });

    verifyForm.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(verifyForm);
        try {
            const response = await fetch('api/verify_coa.php', { 
                method: 'POST', 
                body: formData 
            });
            const result = await response.json();
            verifyResult.classList.remove('d-none');
            if (result.status === 'success') {
                verifyResult.innerHTML = `
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center gap-3">
                            <img src="${result.data.img}" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                            <div>
                                <div class="fw-bold">Verified Genuine: ${result.data.title}</div>
                                <div class="x-small">Collector: ${result.data.collector}</div>
                                <div class="x-small text-secondary">${result.data.specs}</div>
                            </div>
                        </div>
                    </div>`;
            } else { verifyResult.innerHTML = `<div class="alert alert-danger text-center">${result.message}</div>`; }
        } catch (err) { verifyResult.innerHTML = `<div class="alert alert-danger text-center">Failed to verify.</div>`; }
    };

    // Magnifier Logic
    const magnifierContainer = document.getElementById('magnifierContainer');
    const modalArtImg = document.getElementById('modalArtImg');
    const magnifierLoupe = document.getElementById('magnifierLoupe');
    if (magnifierContainer && modalArtImg && magnifierLoupe) {
        magnifierContainer.onmousemove = (e) => {
            const rect = magnifierContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            magnifierLoupe.style.display = 'block';
            magnifierLoupe.style.left = (x - 75) + 'px';
            magnifierLoupe.style.top = (y - 75) + 'px';
            magnifierLoupe.style.backgroundImage = `url(${modalArtImg.src})`;
            magnifierLoupe.style.backgroundSize = (rect.width * 2) + 'px ' + (rect.height * 2) + 'px';
            magnifierLoupe.style.backgroundPosition = `-${(x * 2) - 75}px -${(y * 2) - 75}px`;
        };
        magnifierContainer.onmouseleave = () => magnifierLoupe.style.display = 'none';
    }

    // Secret Admin Shortcut
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.shiftKey && e.code === 'KeyA') window.location.href = 'admin/index.php';
    });

    fetchArtworks();
});
