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
            <button type="button" class="ris-cal-nav" data-nav="-1" aria-label="পূর্বের মাস">‹</button>
            <div class="ris-cal-title"></div>
            <button type="button" class="ris-cal-nav" data-nav="1" aria-label="পরবর্তী মাস">›</button>
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