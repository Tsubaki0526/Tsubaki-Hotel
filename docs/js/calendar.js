/**
 * Hotel Calendar Component
 * Disponibilidad real con reservas demo
 */

class HotelCalendar {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.checkIn = null;
        this.checkOut = null;
        this.selectedRoomType = null;

        this.roomTypes = [
            { id: 1, name: 'Single', price: 1000, total: 5 },
            { id: 2, name: 'Double', price: 1500, total: 8 },
            { id: 3, name: 'Triple', price: 2000, total: 4 },
            { id: 4, name: 'Family', price: 3000, total: 3 },
            { id: 5, name: 'King Sized', price: 5500, total: 2 },
            { id: 6, name: 'Master Suite', price: 6500, total: 2 },
            { id: 7, name: 'Mini-Suite', price: 3600, total: 3 },
            { id: 8, name: 'Connecting Rooms', price: 8000, total: 2 },
            { id: 9, name: 'Presidential Suite', price: 21000, total: 1 },
            { id: 10, name: 'Murphy Room', price: 6900, total: 2 }
        ];

        // Reservas demo pre-existentes
        const today = new Date();
        const y = today.getFullYear();
        const m = today.getMonth();
        this.bookings = [
            { roomTypeId: 1, checkIn: new Date(y, m, 10), checkOut: new Date(y, m, 14) },
            { roomTypeId: 1, checkIn: new Date(y, m, 20), checkOut: new Date(y, m, 23) },
            { roomTypeId: 2, checkIn: new Date(y, m, 8),  checkOut: new Date(y, m, 12) },
            { roomTypeId: 2, checkIn: new Date(y, m, 15), checkOut: new Date(y, m, 18) },
            { roomTypeId: 2, checkIn: new Date(y, m + 1, 5), checkOut: new Date(y, m + 1, 10) },
            { roomTypeId: 3, checkIn: new Date(y, m, 12), checkOut: new Date(y, m, 16) },
            { roomTypeId: 4, checkIn: new Date(y, m, 6),  checkOut: new Date(y, m, 9) },
            { roomTypeId: 4, checkIn: new Date(y, m + 1, 12), checkOut: new Date(y, m + 1, 16) },
            { roomTypeId: 5, checkIn: new Date(y, m, 1),  checkOut: new Date(y, m, 5) },
            { roomTypeId: 5, checkIn: new Date(y, m, 22), checkOut: new Date(y, m, 28) },
            { roomTypeId: 5, checkIn: new Date(y, m + 1, 10), checkOut: new Date(y, m + 1, 15) },
            { roomTypeId: 6, checkIn: new Date(y, m, 3),  checkOut: new Date(y, m, 7) },
            { roomTypeId: 7, checkIn: new Date(y, m, 18), checkOut: new Date(y, m, 22) },
            { roomTypeId: 8, checkIn: new Date(y, m, 9),  checkOut: new Date(y, m, 13) },
            { roomTypeId: 8, checkIn: new Date(y, m + 1, 8), checkOut: new Date(y, m + 1, 12) },
            { roomTypeId: 9, checkIn: new Date(y, m, 5),  checkOut: new Date(y, m, 12) },
            { roomTypeId: 9, checkIn: new Date(y, m + 1, 1), checkOut: new Date(y, m + 1, 8) },
            { roomTypeId: 10, checkIn: new Date(y, m, 14), checkOut: new Date(y, m, 17) },
        ];

        this.monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        this.dayNames = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        this.onDateSelect = options.onDateSelect || null;
        this.render();
    }

    _dateKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    }

    _isPast(date) {
        const today = new Date(); today.setHours(0,0,0,0);
        return date < today;
    }

    _isBooked(roomTypeId, date) {
        const key = this._dateKey(date);
        return this.bookings.some(b =>
            b.roomTypeId === roomTypeId &&
            this._dateKey(b.checkIn) <= key &&
            key < this._dateKey(b.checkOut)
        );
    }

    _countBooked(roomTypeId, date) {
        const key = this._dateKey(date);
        return this.bookings.filter(b =>
            b.roomTypeId === roomTypeId &&
            this._dateKey(b.checkIn) <= key &&
            key < this._dateKey(b.checkOut)
        ).length;
    }

    _isSelected(date) {
        const key = this._dateKey(date);
        if (this.checkIn && this._dateKey(this.checkIn) === key) return 'checkin';
        if (this.checkOut && this._dateKey(this.checkOut) === key) return 'checkout';
        if (this.checkIn && this.checkOut && date > this.checkIn && date < this.checkOut) return 'range';
        return false;
    }

    _hasBookedInRange(roomTypeId, start, end) {
        for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
            if (this._isBooked(roomTypeId, d)) return true;
        }
        return false;
    }

    _getStatus(date) {
        if (this._isPast(date)) return 'past';
        const sel = this._isSelected(date);
        if (sel) return sel;
        if (!this.selectedRoomType) return 'neutral';
        const room = this.roomTypes.find(r => r.id === this.selectedRoomType);
        if (!room) return 'neutral';
        const booked = this._countBooked(this.selectedRoomType, date);
        const avail = room.total - booked;
        if (avail <= 0) return 'unavailable';
        if (avail <= 1) return 'limited';
        return 'available';
    }

    _getAvail(roomTypeId, date) {
        const room = this.roomTypes.find(r => r.id === roomTypeId);
        if (!room) return null;
        const booked = this._countBooked(roomTypeId, date);
        return { available: room.total - booked, total: room.total };
    }

    render() {
        const months = this._getMonthsToShow();
        let html = `<div class="calendar-wrapper">`;

        html += `<div class="calendar-room-selector">
            <label><i class="fas fa-bed"></i> Selecciona habitación para ver disponibilidad:</label>
            <select id="calendarRoomSelect" class="calendar-room-select">
                <option value="">-- Selecciona una habitación --</option>
                ${this.roomTypes.map(r => `<option value="${r.id}" ${this.selectedRoomType==r.id?'selected':''}>${r.name} - $${r.price.toLocaleString()}/noche</option>`).join('')}
            </select>
        </div>`;

        html += `<div class="calendar-months">`;
        months.forEach(m => { html += this._renderMonth(m.year, m.month); });
        html += `</div>`;

        html += `<div class="calendar-legend">
            <div class="legend-item"><span class="legend-dot available"></span> Disponible</div>
            <div class="legend-item"><span class="legend-dot limited"></span> Última disponible</div>
            <div class="legend-item"><span class="legend-dot unavailable"></span> Ocupada</div>
            <div class="legend-item"><span class="legend-dot checkin"></span> Check-in</div>
            <div class="legend-item"><span class="legend-dot checkout"></span> Check-out</div>
            <div class="legend-item"><span class="legend-dot range"></span> Estancia</div>
        </div>`;

        if (this.checkIn) {
            html += `<div class="calendar-selection">
                <div class="selection-info">
                    <span><i class="fas fa-calendar-check"></i> <strong>Check-in:</strong> ${this.checkIn.toLocaleDateString('es-ES',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}</span>
                    ${this.checkOut ? `<span><i class="fas fa-calendar-minus"></i> <strong>Check-out:</strong> ${this.checkOut.toLocaleDateString('es-ES',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}</span>` : ''}
                    ${this.checkIn && this.checkOut ? `<span><i class="fas fa-moon"></i> <strong>${Math.ceil((this.checkOut-this.checkIn)/86400000)} noche(s)</strong></span>` : ''}
                </div>
                <button class="btn btn-sm btn-outline calendar-clear" onclick="hotelCalendar.clearSelection()"><i class="fas fa-times"></i> Limpiar</button>
            </div>`;
        }

        if (this.selectedRoomType && this.checkIn && this.checkOut) {
            const overlap = this._hasBookedInRange(this.selectedRoomType, this.checkIn, this.checkOut);
            if (overlap) {
                html += `<div class="calendar-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>No disponible:</strong> Las fechas seleccionadas se solapan con una reserva existente. Por favor elige otras fechas.
                </div>`;
            }
        }

        html += `</div>`;
        this.container.innerHTML = html;

        const select = document.getElementById('calendarRoomSelect');
        if (select) {
            select.addEventListener('change', (e) => {
                this.selectedRoomType = e.target.value ? parseInt(e.target.value) : null;
                this.checkIn = null;
                this.checkOut = null;
                this._syncFormDates();
                this.render();
            });
        }

        this.container.querySelectorAll('.calendar-day:not(.past):not(.unavailable)').forEach(el => {
            el.addEventListener('click', () => this._onDayClick(el));
        });
    }

    _getMonthsToShow() {
        const months = [];
        for (let i = 0; i < 2; i++) {
            let m = this.currentMonth + i;
            let y = this.currentYear;
            if (m > 11) { m -= 12; y++; }
            months.push({ year: y, month: m });
        }
        return months;
    }

    _renderMonth(year, month) {
        let html = `<div class="calendar-month">`;
        html += `<div class="calendar-header">
            <button class="cal-nav" onclick="hotelCalendar.prevMonth()"><i class="fas fa-chevron-left"></i></button>
            <span class="cal-title">${this.monthNames[month]} ${year}</span>
            <button class="cal-nav" onclick="hotelCalendar.nextMonth()"><i class="fas fa-chevron-right"></i></button>
        </div>`;
        html += `<div class="calendar-grid">`;
        this.dayNames.forEach(d => { html += `<div class="calendar-dayname">${d}</div>`; });

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        for (let i = 0; i < firstDay; i++) html += `<div class="calendar-day empty"></div>`;

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const status = this._getStatus(date);
            const key = this._dateKey(date);
            const avail = this.selectedRoomType ? this._getAvail(this.selectedRoomType, date) : null;

            let tooltip = '';
            if (status === 'available' && avail) tooltip = `${avail.available} de ${avail.total} disponibles`;
            else if (status === 'limited' && avail) tooltip = `¡Solo ${avail.available} disponible(s)!`;
            else if (status === 'unavailable') tooltip = 'Ocupada - No disponible';

            html += `<div class="calendar-day ${status}" data-date="${key}" ${tooltip ? `title="${tooltip}"` : ''}>`;
            html += `<span class="day-number">${day}</span>`;
            if (avail && status !== 'past' && status !== 'neutral') {
                html += `<span class="day-avail">${avail.available}/${avail.total}</span>`;
            }
            html += `</div>`;
        }
        html += `</div></div>`;
        return html;
    }

    _onDayClick(el) {
        const dateStr = el.dataset.date;
        const [y, m, d] = dateStr.split('-').map(Number);
        const date = new Date(y, m - 1, d);

        if (!this.selectedRoomType) {
            alert('Primero selecciona un tipo de habitación.');
            return;
        }

        if (!this.checkIn || (this.checkIn && this.checkOut)) {
            this.checkIn = date;
            this.checkOut = null;
        } else if (date <= this.checkIn) {
            this.checkIn = date;
            this.checkOut = null;
        } else {
            if (this._hasBookedInRange(this.selectedRoomType, this.checkIn, date)) {
                alert('Las fechas seleccionadas se solapan con una reserva existente. Por favor elige otras fechas.');
                return;
            }
            this.checkOut = date;
        }

        this.render();
        this._syncFormDates();
        if (this.onDateSelect) this.onDateSelect(this.checkIn, this.checkOut);
    }

    _syncFormDates() {
        const ci = document.querySelector('input[name="check_in"]');
        const co = document.querySelector('input[name="check_out"]');
        if (ci) ci.value = this.checkIn ? this._dateKey(this.checkIn) : '';
        if (co) co.value = this.checkOut ? this._dateKey(this.checkOut) : '';
        this._syncHiddenField();
    }

    _syncHiddenField() {
        const hidden = document.getElementById('roomTypeHidden');
        if (hidden) hidden.value = this.selectedRoomType || '';
    }

    addBooking(roomTypeId, checkIn, checkOut) {
        this.bookings.push({ roomTypeId, checkIn, checkOut });
        this.checkIn = null;
        this.checkOut = null;
        this._syncFormDates();
        this.render();
    }

    isCurrentlyBooked() {
        if (!this.selectedRoomType || !this.checkIn || !this.checkOut) return false;
        return this._hasBookedInRange(this.selectedRoomType, this.checkIn, this.checkOut);
    }

    clearSelection() {
        this.checkIn = null;
        this.checkOut = null;
        this.render();
        this._syncFormDates();
    }

    prevMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) { this.currentMonth = 11; this.currentYear--; }
        this.render();
    }

    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) { this.currentMonth = 0; this.currentYear++; }
        this.render();
    }
}

let hotelCalendar;
document.addEventListener('DOMContentLoaded', function() {
    const cal = document.getElementById('hotelCalendar');
    if (cal) {
        hotelCalendar = new HotelCalendar('hotelCalendar', {
            onDateSelect: function(ci, co) {
                if (ci && co && hotelCalendar.selectedRoomType) {
                    const room = hotelCalendar.roomTypes.find(r => r.id == hotelCalendar.selectedRoomType);
                    if (room) {
                        const nights = Math.ceil((co - ci) / 86400000);
                        updateBookingSummary(room.name, nights, room.price, room.price * nights);
                    }
                }
            }
        });
    }
});

function updateBookingSummary(roomName, nights, pricePerNight, total) {
    let s = document.getElementById('bookingSummary');
    if (!s) {
        s = document.createElement('div');
        s.id = 'bookingSummary';
        s.className = 'booking-summary-card';
        const form = document.querySelector('.booking-form');
        if (form) form.parentNode.insertBefore(s, form);
    }
    s.innerHTML = `
        <h4><i class="fas fa-receipt"></i> Resumen de Reserva</h4>
        <div class="summary-row"><span>Habitación:</span><strong>${roomName}</strong></div>
        <div class="summary-row"><span>Noches:</span><strong>${nights}</strong></div>
        <div class="summary-row"><span>Precio/noche:</span><strong>$${pricePerNight.toLocaleString()}</strong></div>
        <hr>
        <div class="summary-row total"><span>Total:</span><strong>$${total.toLocaleString()}</strong></div>
    `;
}
