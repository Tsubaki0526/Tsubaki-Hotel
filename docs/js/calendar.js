/**
 * Hotel Calendar Component
 * Muestra disponibilidad de habitaciones por fecha
 * Versión estática para GitHub Pages (datos demo)
 */

class HotelCalendar {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
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

        this.monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        this.dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

        this.onDateSelect = options.onDateSelect || null;
        this.onRoomSelect = options.onRoomSelect || null;

        this._generateAvailability();
        this.render();
    }

    _generateAvailability() {
        this.availability = {};
        const today = new Date();
        this.roomTypes.forEach(room => {
            this.availability[room.id] = {};
            for (let d = new Date(today); d <= new Date(today.getFullYear(), today.getMonth() + 3, 0); d.setDate(d.getDate() + 1)) {
                const key = this._dateKey(d);
                const random = Math.random();
                const booked = random < 0.25;
                this.availability[room.id][key] = {
                    available: room.total - (booked ? Math.ceil(Math.random() * room.total * 0.5) : 0),
                    total: room.total
                };
            }
        });
    }

    _dateKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    _isPast(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return date < today;
    }

    _isSelected(date) {
        const key = this._dateKey(date);
        if (this.checkIn && this._dateKey(this.checkIn) === key) return 'checkin';
        if (this.checkOut && this._dateKey(this.checkOut) === key) return 'checkout';
        if (this.checkIn && this.checkOut && date > this.checkIn && date < this.checkOut) return 'range';
        return false;
    }

    _getStatus(date) {
        if (this._isPast(date)) return 'past';
        if (this._isSelected(date)) return this._isSelected(date);
        if (!this.selectedRoomType) return 'neutral';
        const key = this._dateKey(date);
        const avail = this.availability[this.selectedRoomType]?.[key];
        if (!avail) return 'neutral';
        if (avail.available <= 0) return 'unavailable';
        if (avail.available <= 2) return 'limited';
        return 'available';
    }

    render() {
        const months = this._getMonthsToShow();
        let html = `<div class="calendar-wrapper">`;

        html += `<div class="calendar-room-selector">
            <label><i class="fas fa-bed"></i> Selecciona habitación para ver disponibilidad:</label>
            <select id="calendarRoomSelect" class="calendar-room-select">
                <option value="">Ver todas las habitaciones</option>
                ${this.roomTypes.map(r => `<option value="${r.id}" ${this.selectedRoomType == r.id ? 'selected' : ''}>${r.name} - $${r.price.toLocaleString()}/noche</option>`).join('')}
            </select>
        </div>`;

        html += `<div class="calendar-months">`;
        months.forEach(m => { html += this._renderMonth(m.year, m.month); });
        html += `</div>`;

        html += `<div class="calendar-legend">
            <div class="legend-item"><span class="legend-dot available"></span> Disponible</div>
            <div class="legend-item"><span class="legend-dot limited"></span> Últimas habitaciones</div>
            <div class="legend-item"><span class="legend-dot unavailable"></span> No disponible</div>
            <div class="legend-item"><span class="legend-dot checkin"></span> Check-in</div>
            <div class="legend-item"><span class="legend-dot checkout"></span> Check-out</div>
        </div>`;

        if (this.checkIn) {
            html += `<div class="calendar-selection">
                <div class="selection-info">
                    <span><i class="fas fa-calendar-check"></i> <strong>Check-in:</strong> ${this.checkIn.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    ${this.checkOut ? `<span><i class="fas fa-calendar-minus"></i> <strong>Check-out:</strong> ${this.checkOut.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>` : ''}
                    ${this.checkIn && this.checkOut ? `<span><i class="fas fa-moon"></i> <strong>${Math.ceil((this.checkOut - this.checkIn) / 86400000)} noche(s)</strong></span>` : ''}
                </div>
                <button class="btn btn-sm btn-outline calendar-clear" onclick="hotelCalendar.clearSelection()"><i class="fas fa-times"></i> Limpiar</button>
            </div>`;
        }

        html += `</div>`;
        this.container.innerHTML = html;

        const select = document.getElementById('calendarRoomSelect');
        if (select) {
            select.addEventListener('change', (e) => {
                this.selectedRoomType = e.target.value ? parseInt(e.target.value) : null;
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

        for (let i = 0; i < firstDay; i++) {
            html += `<div class="calendar-day empty"></div>`;
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const status = this._getStatus(date);
            const key = this._dateKey(date);
            const avail = this.selectedRoomType ? this.availability[this.selectedRoomType]?.[key] : null;

            let tooltip = '';
            if (status === 'available') tooltip = `${avail.available} de ${avail.total} disponibles`;
            else if (status === 'limited') tooltip = `¡Solo ${avail.available} disponible(s)!`;
            else if (status === 'unavailable') tooltip = 'Sin disponibilidad';

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

        if (!this.checkIn || (this.checkIn && this.checkOut)) {
            this.checkIn = date;
            this.checkOut = null;
        } else if (date > this.checkIn) {
            this.checkOut = date;
        } else {
            this.checkIn = date;
            this.checkOut = null;
        }

        this.render();
        this._syncFormDates();

        if (this.onDateSelect) {
            this.onDateSelect(this.checkIn, this.checkOut);
        }
    }

    _syncFormDates() {
        const ciInput = document.querySelector('input[name="check_in"]');
        const coInput = document.querySelector('input[name="check_out"]');
        if (ciInput && this.checkIn) ciInput.value = this._dateKey(this.checkIn);
        if (coInput && this.checkOut) coInput.value = this._dateKey(this.checkOut);
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
document.addEventListener('DOMContentLoaded', function () {
    const calContainer = document.getElementById('hotelCalendar');
    if (calContainer) {
        hotelCalendar = new HotelCalendar('hotelCalendar', {
            onDateSelect: function (checkIn, checkOut) {
                const roomSelect = document.querySelector('select[name="room_type_id"]');
                if (roomSelect && roomSelect.value && checkIn && checkOut && hotelCalendar.selectedRoomType) {
                    const room = hotelCalendar.roomTypes.find(r => r.id == hotelCalendar.selectedRoomType);
                    if (room) {
                        const nights = Math.ceil((checkOut - checkIn) / 86400000);
                        const total = room.price * nights;
                        updateBookingSummary(room.name, nights, room.price, total);
                    }
                }
            }
        });
    }

    const roomSelect = document.querySelector('select[name="room_type_id"]');
    if (roomSelect) {
        roomSelect.addEventListener('change', function () {
            if (hotelCalendar) {
                hotelCalendar.selectedRoomType = this.value ? parseInt(this.value) : null;
                hotelCalendar.render();
            }
        });
    }
});

function updateBookingSummary(roomName, nights, pricePerNight, total) {
    let summary = document.getElementById('bookingSummary');
    if (!summary) {
        summary = document.createElement('div');
        summary.id = 'bookingSummary';
        summary.className = 'booking-summary-card';
        const form = document.querySelector('.booking-form');
        if (form) form.parentNode.insertBefore(summary, form.nextSibling);
    }
    summary.innerHTML = `
        <h4><i class="fas fa-receipt"></i> Resumen de Reserva</h4>
        <div class="summary-row"><span>Habitación:</span><strong>${roomName}</strong></div>
        <div class="summary-row"><span>Noches:</span><strong>${nights}</strong></div>
        <div class="summary-row"><span>Precio/noche:</span><strong>$${pricePerNight.toLocaleString()}</strong></div>
        <hr>
        <div class="summary-row total"><span>Total:</span><strong>$${total.toLocaleString()}</strong></div>
    `;
}
