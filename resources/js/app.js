import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

(() => {
    const ISO_RE = /^(\d{4})-(\d{2})-(\d{2})$/;
    const DMY_RE = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const ISO_DT_RE = /^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/;
    const DMY_DT_RE = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;

    const isDatetime = (el) => el.dataset.dateMask === 'datetime';

    const dmyFromIso = (v) => {
        const m = ISO_RE.exec(v);
        return m ? `${m[3]}/${m[2]}/${m[1]}` : null;
    };

    const isoFromDmy = (v) => {
        const m = DMY_RE.exec(v);
        return m ? `${m[3]}-${m[2]}-${m[1]}` : null;
    };

    const dmyDtFromIso = (v) => {
        const m = ISO_DT_RE.exec(v);
        return m ? `${m[3]}/${m[2]}/${m[1]} ${m[4]}:${m[5]}` : null;
    };

    const isoDtFromDmyDt = (v) => {
        const m = DMY_DT_RE.exec(v);
        return m ? `${m[3]}-${m[2]}-${m[1]} ${m[4]}:${m[5]}` : null;
    };

    const toIso = (el, v) => (isDatetime(el) ? isoDtFromDmyDt(v) : isoFromDmy(v));

    const toDisplay = (el, v) => (isDatetime(el) ? dmyDtFromIso(v) : dmyFromIso(v));

    const maskValue = (el) => {
        const max = isDatetime(el) ? 12 : 8;
        const digits = el.value.replace(/\D/g, '').slice(0, max);
        let out = digits.slice(0, 2);
        if (digits.length > 2) out += '/' + digits.slice(2, 4);
        if (digits.length > 4) out += '/' + digits.slice(4, 8);
        if (isDatetime(el) && digits.length > 8) out += ' ' + digits.slice(8, 10);
        if (isDatetime(el) && digits.length > 10) out += ':' + digits.slice(10, 12);
        if (el.value !== out) el.value = out;
    };

    const prepareForm = (form) => {
        form.querySelectorAll('input[data-date-mask]').forEach((el) => {
            const iso = toIso(el, el.value);
            if (iso) el.value = iso;
        });
    };

    const restoreForm = (form) => {
        form.querySelectorAll('input[data-date-mask]').forEach((el) => {
            const dmy = toDisplay(el, el.value);
            if (dmy) el.value = dmy;
        });
    };

    const initDateMasks = (root = document) => {
        root.querySelectorAll('input[data-date-mask]').forEach((el) => {
            el.setAttribute('type', 'text');
            el.setAttribute('inputmode', 'numeric');
            el.setAttribute('autocomplete', 'off');
            if (!el.hasAttribute('maxlength')) el.setAttribute('maxlength', isDatetime(el) ? '16' : '10');
            if (!el.getAttribute('placeholder')) {
                el.setAttribute('placeholder', isDatetime(el) ? 'dd/mm/yyyy hh:mm' : 'dd/mm/yyyy');
            }
            const display = toDisplay(el, el.value);
            if (display) el.value = display;
            el.addEventListener('input', () => maskValue(el));
            if (el.dataset.calendar !== 'none') {
                ['focus', 'click'].forEach((ev) => el.addEventListener(ev, () => openCalendar(el)));
            }
        });
    };

    const BN_MONTHS = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
    const BN_DAYS = ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহঃ', 'শুক্র', 'শনি'];
    const pad2 = (n) => String(n).padStart(2, '0');

    let activeField = null;
    let calPopup = null;
    let calYear = new Date().getFullYear();
    let calMonth = new Date().getMonth();
    let calTimeH = null;
    let calTimeM = null;

    const parseDmy = (v) => {
        const m = DMY_RE.exec(v);
        return m ? { y: +m[3], m: +m[2], d: +m[1] } : null;
    };

    const closeCalendar = () => {
        if (calPopup) {
            calPopup.remove();
            calPopup = null;
        }
        activeField = null;
    };

    const positionCalendar = () => {
        if (!calPopup || !activeField) return;
        const r = activeField.getBoundingClientRect();
        const pw = calPopup.offsetWidth || 288;
        const ph = calPopup.offsetHeight || 320;
        let left = Math.min(r.left, window.innerWidth - pw - 8);
        if (left < 8) left = 8;
        let top = r.bottom + 6;
        if (top + ph > window.innerHeight - 8) {
            top = Math.max(8, r.top - ph - 6);
        }
        calPopup.style.left = `${left}px`;
        calPopup.style.top = `${top}px`;
    };

    const renderCalendar = () => {
        if (!calPopup) return;
        calPopup.innerHTML = '';

        const header = document.createElement('div');
        header.className = 'ris-cal-header';
        header.innerHTML = `
            <button type="button" class="ris-cal-nav cursor-pointer" data-nav="-1" aria-label="পূর্বের মাস">‹</button>
            <div class="ris-cal-title"></div>
            <button type="button" class="ris-cal-nav cursor-pointer" data-nav="1" aria-label="পরবর্তী মাস">›</button>
        `;

        const week = document.createElement('div');
        week.className = 'ris-cal-week';
        BN_DAYS.forEach((d) => {
            const s = document.createElement('span');
            s.textContent = d;
            week.appendChild(s);
        });

        const grid = document.createElement('div');
        grid.className = 'ris-cal-grid';

        const first = new Date(calYear, calMonth, 1);
        const offset = first.getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

        for (let i = 0; i < offset; i++) {
            const b = document.createElement('span');
            b.className = 'ris-cal-blank';
            grid.appendChild(b);
        }

        const today = new Date();
        const isToday = (d) => today.getFullYear() === calYear && today.getMonth() === calMonth && today.getDate() === d;

        for (let d = 1; d <= daysInMonth; d++) {
            const c = document.createElement('button');
            c.type = 'button';
            c.className = 'ris-cal-day';
            if (isToday(d)) c.classList.add('ris-cal-today');
            if (calPopup.dataset.selected === `${d}/${calMonth + 1}/${calYear}`) {
                c.classList.add('ris-cal-selected');
            }
            c.textContent = String(d);
            c.dataset.day = String(d);
            c.addEventListener('click', () => pickDate(d));
            grid.appendChild(c);
        }

        calPopup.innerHTML = '';
        calPopup.append(header, week, grid);
        if (isDatetime(activeField)) {
            const timeRow = document.createElement('div');
            timeRow.className = 'ris-cal-time';
            const hSel = document.createElement('select');
            const mSel = document.createElement('select');
            for (let h = 0; h < 24; h++) {
                const o = document.createElement('option');
                o.value = String(h);
                o.textContent = pad2(h);
                if (h === calTimeH) o.selected = true;
                hSel.appendChild(o);
            }
            for (let m = 0; m < 60; m++) {
                const o = document.createElement('option');
                o.value = String(m);
                o.textContent = pad2(m);
                if (m === calTimeM) o.selected = true;
                mSel.appendChild(o);
            }
            hSel.addEventListener('change', () => { calTimeH = +hSel.value; });
            mSel.addEventListener('change', () => { calTimeM = +mSel.value; });
            timeRow.append('সময়: ', hSel, ' : ', mSel);
            calPopup.appendChild(timeRow);
        }
        const footer = document.createElement('div');
        footer.className = 'ris-cal-footer';
        const todayBtn = document.createElement('button');
        todayBtn.type = 'button';
        todayBtn.className = 'ris-cal-today-btn';
        todayBtn.textContent = 'আজ';
        todayBtn.addEventListener('click', () => {
            const now = new Date();
            calYear = now.getFullYear();
            calMonth = now.getMonth();
            if (isDatetime(activeField)) {
                calTimeH = now.getHours();
                calTimeM = now.getMinutes();
            }
            pickDate(now.getDate());
        });
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'ris-cal-clear-btn';
        clearBtn.textContent = 'মুছুন';
        clearBtn.addEventListener('click', () => {
            if (activeField) {
                activeField.value = '';
                activeField.dispatchEvent(new Event('input', { bubbles: true }));
                activeField.dispatchEvent(new Event('change', { bubbles: true }));
                activeField.dispatchEvent(new Event('blur', { bubbles: true }));
            }
            closeCalendar();
        });
        footer.append(todayBtn, clearBtn);
        calPopup.appendChild(footer);

        const title = header.querySelector('.ris-cal-title');
        title.textContent = `${BN_MONTHS[calMonth]} ${calYear}`;
        header.querySelector('[data-nav="-1"]').addEventListener('click', () => shiftMonth(-1));
        header.querySelector('[data-nav="1"]').addEventListener('click', () => shiftMonth(1));

        positionCalendar();
    };

    const renderTitle = () => {
        const t = calPopup?.querySelector('.ris-cal-title');
        if (t) t.textContent = `${BN_MONTHS[calMonth]} ${calYear}`;
    };

    const renderGrid = () => {
        if (!calPopup) return;
        const grid = calPopup.querySelector('.ris-cal-grid');
        if (!grid) return;
        const first = new Date(calYear, calMonth, 1);
        grid.innerHTML = '';
        for (let i = 0; i < first.getDay(); i++) {
            const b = document.createElement('span');
            b.className = 'ris-cal-blank';
            grid.appendChild(b);
        }
        const today = new Date();
        for (let d = 1; d <= new Date(calYear, calMonth + 1, 0).getDate(); d++) {
            const c = document.createElement('button');
            c.type = 'button';
            c.className = 'ris-cal-day';
            if (today.getFullYear() === calYear && today.getMonth() === calMonth && today.getDate() === d) {
                c.classList.add('ris-cal-today');
            }
            if (calPopup.dataset.selected === `${d}/${calMonth + 1}/${calYear}`) {
                c.classList.add('ris-cal-selected');
            }
            c.textContent = String(d);
            c.dataset.day = String(d);
            c.addEventListener('click', () => pickDate(d));
            grid.appendChild(c);
        }
    };

    const shiftMonth = (delta) => {
        calMonth += delta;
        if (calMonth < 0) { calMonth = 11; calYear--; }
        if (calMonth > 11) { calMonth = 0; calYear++; }
        renderTitle();
        renderGrid();
        positionCalendar();
    };

    const pickDate = (day) => {
        const field = activeField;
        if (!field) return;
        const selected = `${pad2(day)}/${pad2(calMonth + 1)}/${calYear}`;
        calPopup.dataset.selected = `${day}/${calMonth + 1}/${calYear}`;
        let value = selected;
        if (isDatetime(field)) {
            value = `${selected} ${pad2(calTimeH ?? 0)}:${pad2(calTimeM ?? 0)}`;
        }
        closeCalendar();
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
        field.dispatchEvent(new Event('blur', { bubbles: true }));
    };

    const openCalendar = (field) => {
        if (field !== activeField) closeCalendar();
        activeField = field;

        const sel = parseDmy(field.value);
        if (sel) {
            calYear = sel.y;
            calMonth = sel.m - 1;
        } else {
            const now = new Date();
            calYear = now.getFullYear();
            calMonth = now.getMonth();
        }
        if (isDatetime(field)) {
            const m = DMY_DT_RE.exec(field.value);
            if (m) {
                calTimeH = +m[4];
                calTimeM = +m[5];
            } else {
                const now = new Date();
                calTimeH = now.getHours();
                calTimeM = now.getMinutes();
            }
        }

        if (!calPopup) {
            calPopup = document.createElement('div');
            calPopup.className = 'ris-cal-popup';
            calPopup.dataset.selected = sel ? `${sel.d}/${sel.m}/${sel.y}` : '';
            document.body.appendChild(calPopup);
        }
        renderCalendar();
    };

    document.addEventListener('submit', (e) => {
        prepareForm(e.target);

        setTimeout(() => restoreForm(e.target), 0);
    }, true);

    document.addEventListener('click', (e) => {
        if (!calPopup) return;
        if (calPopup.contains(e.target)) return;
        if (activeField && activeField.contains(e.target)) return;
        closeCalendar();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeCalendar();
    });

    ['scroll', 'resize'].forEach((ev) => {
        window.addEventListener(ev, () => {
            if (activeField) positionCalendar();
        }, true);
    });

    document.addEventListener('DOMContentLoaded', () => initDateMasks());

    window.RisDateMask = { prepare: prepareForm, restore: restoreForm, init: initDateMasks };
})();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.hero-swiper').forEach((el) => {
        new Swiper(el, {
            loop: true,
            speed: 800,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
        });
    });

    document.querySelectorAll('.campus-life-swiper').forEach((el) => {
        new Swiper(el, {
            loop: true,
            speed: 600,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 16 },
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 2, spaceBetween: 24 },
            },
        });
    });

    document.querySelectorAll('.message-swiper').forEach((el) => {
        new Swiper(el, {
            loop: true,
            speed: 600,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 16 },
                1024: { slidesPerView: 2, spaceBetween: 24 },
            },
        });
    });

    document.querySelectorAll('.teacher-swiper').forEach((el) => {
        new Swiper(el, {
            loop: true,
            speed: 600,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 16 },
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
                1280: { slidesPerView: 4, spaceBetween: 24 },
            },
        });
    });

    document.querySelectorAll('.testimonial-swiper').forEach((el) => {
        const host = el.parentElement;
        new Swiper(el, {
            loop: true,
            speed: 600,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: host.querySelector('.testimonial-btn-next'),
                prevEl: host.querySelector('.testimonial-btn-prev'),
            },
            pagination: {
                el: host.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 16 },
                768: { slidesPerView: 2, spaceBetween: 24 },
                1280: { slidesPerView: 3, spaceBetween: 28 },
            },
        });
    });

    document.querySelectorAll('.gallery-swiper').forEach((el) => {
        const host = el.parentElement;
        new Swiper(el, {
            loop: true,
            speed: 600,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: host.querySelector('.gallery-btn-next'),
                prevEl: host.querySelector('.gallery-btn-prev'),
            },
            pagination: {
                el: host.querySelector('.swiper-pagination'),
                clickable: true,
            },
            breakpoints: {
                0: { slidesPerView: 1, spaceBetween: 16 },
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
                1280: { slidesPerView: 4, spaceBetween: 24 },
            },
        });
    });

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealSelector = '.reveal, .reveal-left, .reveal-right, .reveal-zoom';
    const revealEls = document.querySelectorAll(revealSelector);

    document.querySelectorAll('.reveal-stagger').forEach((parent) => {
        [...parent.querySelectorAll(`:scope > ${revealSelector}`)].forEach((child, i) => {
            child.style.transitionDelay = `${Math.min(i * 90, 720)}ms`;
        });
    });

    if (!prefersReduced && revealEls.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        io.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -48px 0px' }
        );
        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('revealed'));
    }

    const countEls = document.querySelectorAll('[data-count]');
    if (countEls.length) {
        const format = (n) => new Intl.NumberFormat('bn-BD').format(n);
        const ioCount = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                ioCount.unobserve(el);
                const target = parseInt(el.dataset.count, 10) || 0;
                if (prefersReduced) {
                    el.textContent = format(target);
                    return;
                }
                const dur = 1500;
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - start) / dur, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = format(Math.round(target * eased));
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
        }, { threshold: 0.4 });
        countEls.forEach((el) => ioCount.observe(el));
    }
});

(() => {
    const getSrc = (el) => {
        if (el.dataset.src) return el.dataset.src.trim();
        const img = el.querySelector('img');
        return img ? (img.getAttribute('src') || '').trim() : '';
    };

    const collect = () => {
        const seen = new Set();
        return [...document.querySelectorAll('.lightbox-trigger')].filter((el) => {
            const src = getSrc(el);
            if (!src || seen.has(src)) return false;
            seen.add(src);
            return true;
        });
    };

    let lightbox = null;
    let group = [];
    let currentIndex = 0;

    const buildLightbox = () => {
        const wrap = document.createElement('div');
        wrap.className = 'lightbox hidden fixed inset-0 z-[999] items-center justify-center p-4 sm:p-6';
        wrap.innerHTML = `
            <div class="lightbox-backdrop absolute inset-0 bg-black/90"></div>
            <button type="button" class="lightbox-close absolute top-4 right-4 sm:top-5 sm:right-5 z-10 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center cursor-pointer transition-colors" aria-label="বন্ধ করুন">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <button type="button" class="lightbox-prev absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center cursor-pointer transition-colors" aria-label="পূর্ববর্তী">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <figure class="relative max-w-6xl w-full mx-auto">
                <img class="lightbox-img max-h-[82vh] w-auto max-w-full mx-auto rounded-xl object-contain shadow-2xl" alt="">
                <figcaption class="lightbox-caption hidden text-center text-white mt-4"></figcaption>
                <div class="lightbox-counter absolute -top-9 right-0 text-white/70 text-sm font-medium"></div>
            </figure>
            <button type="button" class="lightbox-next absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center cursor-pointer transition-colors" aria-label="পরবর্তী">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        `;
        document.body.appendChild(wrap);
        return wrap;
    };

    const renderCaption = (g) => {
        const fig = lightbox.querySelector('.lightbox-caption');
        fig.innerHTML = '';
        if (g.category) {
            const chip = document.createElement('span');
            chip.className = 'inline-block px-2.5 py-0.5 bg-ris-primary text-white text-xs font-medium rounded-full mb-2';
            chip.textContent = g.category;
            fig.appendChild(chip);
        }
        if (g.title) {
            const t = document.createElement('h4');
            t.className = 'font-heading font-bold text-white text-lg';
            t.textContent = g.title;
            fig.appendChild(t);
        }
        if (g.caption) {
            const p = document.createElement('p');
            p.className = 'text-white/75 text-sm mt-1';
            p.textContent = g.caption;
            fig.appendChild(p);
        }
        fig.classList.toggle('hidden', fig.children.length === 0);
    };

    const showSlide = () => {
        const g = group[currentIndex];
        const img = lightbox.querySelector('.lightbox-img');
        img.src = g.src;
        img.alt = g.title || 'গ্যালারির ছবি';
        lightbox.querySelector('.lightbox-counter').textContent = `${currentIndex + 1} / ${group.length}`;
        renderCaption(g);
    };

    const openLightbox = (el, all) => {
        group = all.map((e) => ({
            src: getSrc(e),
            title: e.dataset.title || '',
            category: e.dataset.category || '',
            caption: e.dataset.caption || '',
        }));
        if (!lightbox) lightbox = buildLightbox();
        const idx = group.findIndex((g) => g.src === getSrc(el));
        currentIndex = idx >= 0 ? idx : 0;
        showSlide();
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    const closeLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.style.overflow = '';
    };

    const shift = (delta) => {
        currentIndex = (currentIndex + delta + group.length) % group.length;
        showSlide();
    };

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.lightbox-trigger');
        if (trigger) {
            e.preventDefault();
            openLightbox(trigger, collect());
            return;
        }
        if (!lightbox || lightbox.classList.contains('hidden')) return;
        if (e.target.closest('.lightbox-backdrop') || e.target.closest('.lightbox-close')) closeLightbox();
        else if (e.target.closest('.lightbox-prev')) shift(-1);
        else if (e.target.closest('.lightbox-next')) shift(1);
    });

    document.addEventListener('keydown', (e) => {
        if (!lightbox || lightbox.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeLightbox();
        else if (e.key === 'ArrowLeft') shift(-1);
        else if (e.key === 'ArrowRight') shift(1);
    });
})();