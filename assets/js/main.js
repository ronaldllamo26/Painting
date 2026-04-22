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
    
    let allArtworks = [];
    let filteredArtworks = [];
    let currentPage = 1;
    const itemsPerPage = 6;

    // Fetch Artworks
    async function fetchArtworks() {
        try {
            const response = await fetch('api/artworks.php');
            const result = await response.json();
            
            if (result.status === 'success') {
                allArtworks = result.data;
                filteredArtworks = [...allArtworks]; // Initial state
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

        // Slice artworks for current page
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedItems = artworks.slice(startIndex, endIndex);

        paginatedItems.forEach(art => {
            const isSold = art.status === 'Sold';
            const isPending = art.status === 'Pending';
            
            const card = `
                <div class="col-lg-4 col-md-6 artwork-item animate__animated animate__fadeIn">
                    <div class="glass-card">
                        <div class="artwork-img-container">
                            <a data-fslightbox="gallery" href="${art.image_url}">
                                <img src="${art.image_url}" class="artwork-img" alt="${art.title}">
                            </a>
                            ${isSold ? '<div class="sold-overlay">SOLD</div>' : ''}
                            ${isPending ? '<div class="pending-overlay">PENDING</div>' : ''}
                        </div>
                        <div class="artwork-info">
                            <h3 class="artwork-title">${art.title}</h3>
                            <p class="artwork-meta">${art.size} | ${art.medium}</p>
                            <p class="artwork-price">₱${parseFloat(art.price).toLocaleString()} ${art.is_negotiable == 1 ? '<small class="text-secondary fw-normal fs-6">(negotiable)</small>' : ''}</p>
                            <button class="btn-gallery" onclick="viewDetails(${art.id})">
                                ${isSold ? 'View Details' : 'Buy Now'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            galleryContainer.innerHTML += card;
        });
        
        renderPagination(totalPages);

        // Refresh Lightbox
        if (typeof refreshFsLightbox === 'function') {
            refreshFsLightbox();
        }
    }

    // Render Pagination Buttons
    function renderPagination(totalPages) {
        const container = document.getElementById('paginationContainer');
        container.innerHTML = '';

        if (totalPages <= 1) return;

        const scrollToGallery = () => {
            const gallerySection = document.getElementById('gallery');
            const offset = 100; // Offset for the fixed navbar
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = gallerySection.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        };

        // Previous Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
        prevLi.onclick = (e) => { 
            if(currentPage > 1) { 
                currentPage--; 
                renderGallery(filteredArtworks); 
                scrollToGallery();
            } 
        };
        container.appendChild(prevLi);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="javascript:void(0)">${i}</a>`;
            li.onclick = (e) => { 
                e.preventDefault();
                currentPage = i; 
                renderGallery(filteredArtworks); 
                scrollToGallery();
            };
            container.appendChild(li);
        }

        // Next Button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
        nextLi.onclick = (e) => { 
            if(currentPage < totalPages) { 
                currentPage++; 
                renderGallery(filteredArtworks); 
                scrollToGallery();
            } 
        };
        container.appendChild(nextLi);
    }

    // Render Filters
    function renderFilters(tags) {
        // Handle the initial "All Artworks" button
        const allBtn = document.querySelector('.filter-btn[data-filter="all"]');
        if (allBtn) {
            allBtn.onclick = () => filterByTag('all', allBtn);
        }

        tags.forEach(tag => {
            const btn = document.createElement('button');
            btn.className = 'filter-btn';
            btn.textContent = tag;
            btn.onclick = () => filterByTag(tag, btn);
            tagFilters.appendChild(btn);
        });
    }

    // Filter Logic
    window.filterByTag = (tag, btn) => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1; // Reset to page 1 on filter

        if (tag === 'all') {
            filteredArtworks = [...allArtworks];
        } else {
            filteredArtworks = allArtworks.filter(art => {
                if (!art.ai_tags) return false;
                const tagsArray = art.ai_tags.split(',').map(t => t.trim().toLowerCase());
                return tagsArray.includes(tag.toLowerCase());
            });
        }
        renderGallery(filteredArtworks);
    };

    // View Details & Buy
    window.viewDetails = (id) => {
        const art = allArtworks.find(a => a.id == id);
        if (!art) return;

        document.getElementById('modalArtTitle').textContent = art.title;
        document.getElementById('modalArtImg').src = art.image_url;
        document.getElementById('modalArtImg').parentElement.href = art.image_url; // Update link href
        document.getElementById('modalArtPrice').innerHTML = `₱${parseFloat(art.price).toLocaleString()} ${art.is_negotiable ? '<small class="text-secondary fw-normal fs-6">(negotiable)</small>' : ''}`;
        document.getElementById('modalArtDetails').textContent = `${art.size} | ${art.medium}`;
        document.getElementById('modalArtDesc').textContent = art.ai_description;
        document.getElementById('checkoutArtId').value = art.id;

        // Reset and show modal
        checkoutForm.reset();
        
        // Default behavior for GCash (since it's now first in select)
        if (paymentMethod.value === 'GCash') {
            gcashSection.classList.remove('d-none');
        } else {
            gcashSection.classList.add('d-none');
        }
        
        if (art.status !== 'Available') {
            document.getElementById('btnPlaceOrder').style.display = 'none';
            document.getElementById('btnNegotiate').classList.add('d-none');
            document.getElementById('negotiablePriceSection').classList.add('d-none');
        } else {
            document.getElementById('btnPlaceOrder').style.display = 'block';
            if (art.is_negotiable == 1) {
                document.getElementById('btnNegotiate').classList.remove('d-none');
                document.getElementById('negotiablePriceSection').classList.remove('d-none');
            } else {
                document.getElementById('btnNegotiate').classList.add('d-none');
                document.getElementById('negotiablePriceSection').classList.add('d-none');
            }
        }

        const modal = new bootstrap.Modal(document.getElementById('buyModal'));
        modal.show();
    };

    // Toggle GCash Section
    paymentMethod.addEventListener('change', (e) => {
        if (e.target.value === 'GCash') {
            gcashSection.classList.remove('d-none');
        } else {
            gcashSection.classList.add('d-none');
        }
    });

    // Handle Checkout
    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(checkoutForm);
        
        // Validation for GCash
        if (paymentMethod.value === 'GCash' && !document.getElementById('receiptInput').files[0]) {
            Swal.fire('Error', 'Please upload your GCash receipt screenshot.', 'error');
            return;
        }

        try {
            const response = await fetch('api/place_order.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('buyModal')).hide();
                Swal.fire({
                    title: 'Order Placed!',
                    text: 'The artist will review your order shortly.',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Something went wrong.', 'error');
        }
    });

    // Live Search
    navbarSearch.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = allArtworks.filter(art => 
            art.title.toLowerCase().includes(query) || 
            (art.ai_tags && art.ai_tags.toLowerCase().includes(query)) ||
            art.medium.toLowerCase().includes(query)
        );
        renderGallery(filtered);
    });

    // Track Order
    trackForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const contact = document.getElementById('trackContact').value;
        
        try {
            const response = await fetch('api/track_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `contact_number=${encodeURIComponent(contact)}`
            });
            const result = await response.json();

            if (result.status === 'success') {
                trackResult.classList.remove('d-none');
                orderList.innerHTML = '';
                
                result.data.forEach(order => {
                    const statusColor = order.order_status === 'Approved' ? 'text-success' : (order.order_status === 'Cancelled' ? 'text-danger' : 'text-warning');
                    const item = `
                        <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                            <img src="${order.image_url}" style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-bold">${order.artwork_title}</h6>
                                <p class="mb-0 x-small text-secondary">Ordered on: ${new Date(order.order_date).toLocaleDateString()}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge ${statusColor.replace('text-', 'bg-')} small">${order.order_status}</span>
                            </div>
                        </div>
                    `;
                    orderList.innerHTML += item;
                });
            } else {
                Swal.fire('Error', result.message, 'error');
                trackResult.classList.add('d-none');
            }
        } catch (error) {
            Swal.fire('Error', 'Failed to track order.', 'error');
        }
    });

    // Secret Admin Shortcut (Alt + Shift + A)
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.shiftKey && e.code === 'KeyA') {
            window.location.href = 'admin/index.php';
        }
    });

    fetchArtworks();
});
