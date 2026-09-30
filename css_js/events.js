/* =========================================
   EVENTS CALENDAR & SYSTEM LOGIC (events.js)
========================================= */

document.addEventListener("DOMContentLoaded", function () {

    // 1. DYNAMIC SYSTEM DATE DETECTION
    const today = new Date();
    
    // Tracks current visible month view (Defaults to actual live year & month)
    let currentViewDate = new Date(today.getFullYear(), today.getMonth(), 1);

    // Helper: Formats month and day to YYYY-MM-DD
    function formatDateKey(year, monthIndex, day) {
        const formattedMonth = String(monthIndex + 1).padStart(2, "0");
        const formattedDay = String(day).padStart(2, "0");
        return `${year}-${formattedMonth}-${formattedDay}`;
    }

    // Dynamic key for today's exact date
    const realTodayKey = formatDateKey(today.getFullYear(), today.getMonth(), today.getDate());
    let selectedDateKey = realTodayKey;

    // Full 12-Month Academic & System Events Database (Key format: YYYY-MM-DD)
    const eventsData = {
        // --- JANUARY ---
        "2026-01-05": [
            { title: "Classes Resume (2nd Semester)", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-01-15": [
            { title: "Departmental Faculty Assembly", time: "1:00 PM - 4:00 PM" }
        ],
        "2026-01-26": [
            { title: "Preliminary Examinations", time: "8:00 AM - 5:00 PM" }
        ],

        // --- FEBRUARY ---
        "2026-02-14": [
            { title: "Campus Valentines Fair & Cultural Show", time: "9:00 AM - 6:00 PM" }
        ],
        "2026-02-20": [
            { title: "Annual Science & Tech Expo", time: "10:00 AM - 4:00 PM" }
        ],
        "2026-02-25": [
            { title: "EDSA People Power Revolution (Holiday)", time: "All Day" }
        ],

        // --- MARCH ---
        "2026-03-09": [
            { title: "Midterm Examinations", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-03-18": [
            { title: "Student Research Congress", time: "9:00 AM - 3:00 PM" }
        ],
        "2026-03-27": [
            { title: "Submission of Midterm Grades", time: "5:00 PM Deadline" }
        ],

        // --- APRIL ---
        "2026-04-02": [
            { title: "Maundy Thursday (Holiday)", time: "All Day" }
        ],
        "2026-04-03": [
            { title: "Good Friday (Holiday)", time: "All Day" }
        ],
        "2026-04-22": [
            { title: "Earth Day Campus Tree Planting", time: "7:00 AM - 11:00 AM" }
        ],

        // --- MAY ---
        "2026-05-01": [
            { title: "Labor Day (Holiday)", time: "All Day" }
        ],
        "2026-05-18": [
            { title: "Final Examinations Week", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-05-29": [
            { title: "Graduation Rites & Baccalaureate Mass", time: "2:00 PM - 7:00 PM" }
        ],

        // --- JUNE ---
        "2026-06-12": [
            { title: "Philippine Independence Day (Holiday)", time: "All Day" }
        ],
        "2026-06-15": [
            { title: "Summer Term Enrollment", time: "8:00 AM - 4:00 PM" }
        ],
        "2026-06-22": [
            { title: "Start of Summer Classes", time: "8:00 AM - 5:00 PM" }
        ],

        // --- JULY ---
        "2026-07-06": [
            { title: "Midterm Examination", time: "8:00 AM - 12:00 PM" }
        ],
        "2026-07-10": [
            { title: "Departmental Meeting", time: "2:00 PM - 4:00 PM" }
        ],
        "2026-07-13": [
            { title: "Student Council Orientation", time: "10:00 AM - 11:30 AM" }
        ],
        "2026-07-20": [
            { title: "ASTRA System Deployment", time: "9:00 AM - 11:00 AM" },
            { title: "Campus Tech Summit", time: "1:00 PM - 5:00 PM" }
        ],
        "2026-07-24": [
            { title: "Submission of Grades", time: "5:00 PM Deadline" }
        ],
        "2026-07-28": [
            { title: "College System Maintenance", time: "3:00 PM - 5:00 PM" }
        ],

        // --- AUGUST ---
        "2026-08-03": [
            { title: "Opening of Classes (1st Sem)", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-08-10": [
            { title: "Student Council Elections", time: "9:00 AM - 3:00 PM" }
        ],
        "2026-08-14": [
            { title: "Faculty Workshop & Seminar", time: "1:00 PM - 4:00 PM" }
        ],
        "2026-08-21": [
            { title: "Ninoy Aquino Day (Holiday)", time: "All Day" }
        ],
        "2026-08-28": [
            { title: "Campus Freshmen Orientation", time: "9:00 AM - 12:00 PM" }
        ],

        // --- SEPTEMBER ---
        "2026-09-07": [
            { title: "College Foundation Week Launch", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-09-15": [
            { title: "Inter-Department Sportsfest", time: "8:00 AM - 6:00 PM" }
        ],
        "2026-09-28": [
            { title: "First Quarter Assessment", time: "8:00 AM - 5:00 PM" }
        ],

        // --- OCTOBER ---
        "2026-10-05": [
            { title: "World Teachers' Day Celebration", time: "9:00 AM - 3:00 PM" }
        ],
        "2026-10-19": [
            { title: "Midterm Exams (1st Sem)", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-10-30": [
            { title: "Semestral Break Begins", time: "All Day" }
        ],

        // --- NOVEMBER ---
        "2026-11-01": [
            { title: "All Saints' Day (Holiday)", time: "All Day" }
        ],
        "2026-11-09": [
            { title: "Resume of Classes", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-11-30": [
            { title: "Bonifacio Day (Holiday)", time: "All Day" }
        ],

        // --- DECEMBER ---
        "2026-12-08": [
            { title: "Feast of the Immaculate Conception", time: "All Day" }
        ],
        "2026-12-14": [
            { title: "Final Examinations (1st Sem)", time: "8:00 AM - 5:00 PM" }
        ],
        "2026-12-18": [
            { title: "Annual Christmas Party & Convocation", time: "2:00 PM - 8:00 PM" }
        ],
        "2026-12-25": [
            { title: "Christmas Day (Holiday)", time: "All Day" }
        ],
        "2026-12-30": [
            { title: "Rizal Day (Holiday)", time: "All Day" }
        ]
    };

    // DOM Elements
    const calendarMonthYear = document.getElementById("calendarMonthYear");
    const calendarGrid = document.getElementById("calendarGrid");
    const eventsList = document.getElementById("eventsList");
    const prevBtn = document.getElementById("prevMonthBtn");
    const nextBtn = document.getElementById("nextMonthBtn");

    const monthNames = [
        "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
        "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
    ];

    // Initialize Calendar
    function renderCalendar() {
        if (!calendarGrid || !calendarMonthYear) return;

        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();

        // Update Month Title Header
        calendarMonthYear.textContent = `${monthNames[month]} ${year}`;

        // Clear existing grid
        calendarGrid.innerHTML = "";

        const firstDayIndex = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();
        const prevLastDay = new Date(year, month, 0).getDate();

        // Previous month filler days (faded)
        for (let x = firstDayIndex; x > 0; x--) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("cal-day", "empty");
            dayDiv.textContent = prevLastDay - x + 1;
            calendarGrid.appendChild(dayDiv);
        }

        // Current month days
        for (let day = 1; day <= totalDays; day++) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("cal-day");
            dayDiv.textContent = day;

            const dateKey = formatDateKey(year, month, day);

            // Highlight real "Today"
            if (dateKey === realTodayKey) {
                dayDiv.classList.add("today");
            }

            // Check if day has event
            if (eventsData[dateKey]) {
                dayDiv.classList.add("has-event");
            }

            // Check if day is currently selected
            if (dateKey === selectedDateKey) {
                dayDiv.classList.add("selected");
            }

            // 1. CLICK EVENT: Select day and display events
            dayDiv.addEventListener("click", function () {
                document.querySelectorAll(".calendar-grid .cal-day").forEach(el => {
                    el.classList.remove("selected");
                });

                dayDiv.classList.add("selected");
                selectedDateKey = dateKey;
                displayEventsForDate(dateKey);
            });

            // 2. HOVER EVENT: Preview events on left panel
            dayDiv.addEventListener("mouseenter", function () {
                displayEventsForDate(dateKey);
            });

            // 3. MOUSE LEAVE: Restore previous view
            dayDiv.addEventListener("mouseleave", function () {
                if (selectedDateKey) {
                    displayEventsForDate(selectedDateKey);
                } else {
                    displayEventsForMonth(year, month);
                }
            });

            calendarGrid.appendChild(dayDiv);
        }

        // Next month filler days (faded)
        const totalSlots = firstDayIndex + totalDays;
        const nextDays = (7 - (totalSlots % 7)) % 7;
        for (let j = 1; j <= nextDays; j++) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("cal-day", "empty");
            dayDiv.textContent = j;
            calendarGrid.appendChild(dayDiv);
        }

        // AUTO-LOAD EVENTS DISPLAY
        if (selectedDateKey && selectedDateKey.startsWith(`${year}-${String(month + 1).padStart(2, "0")}`)) {
            displayEventsForDate(selectedDateKey);
        } else {
            displayEventsForMonth(year, month);
        }
    }

    // Display ALL events for the visible month automatically
    function displayEventsForMonth(year, monthIndex) {
        if (!eventsList) return;

        eventsList.innerHTML = "";

        const monthKeyPrefix = `${year}-${String(monthIndex + 1).padStart(2, "0")}`;
        
        const monthlyEventKeys = Object.keys(eventsData)
            .filter(key => key.startsWith(monthKeyPrefix))
            .sort();

        if (monthlyEventKeys.length > 0) {
            monthlyEventKeys.forEach(dateKey => {
                const dayNumber = dateKey.split("-")[2];
                const dayEvents = eventsData[dateKey];

                dayEvents.forEach(evt => {
                    const item = document.createElement("div");
                    item.classList.add("event-item");

                    const title = document.createElement("div");
                    title.classList.add("event-item-title");
                    title.textContent = `${monthNames[monthIndex].slice(0, 3)} ${parseInt(dayNumber, 10)}: ${evt.title}`;

                    const time = document.createElement("div");
                    time.classList.add("event-item-time");
                    time.innerHTML = `<i class="fa-regular fa-clock"></i> ${evt.time}`;

                    item.appendChild(title);
                    item.appendChild(time);
                    eventsList.appendChild(item);
                });
            });
        } else {
            eventsList.innerHTML = `<p class="no-events-placeholder">No upcoming events scheduled for ${monthNames[monthIndex]}</p>`;
        }
    }

    // Display events for a single specific date (When clicked or hovered)
    function displayEventsForDate(dateKey) {
        if (!eventsList) return;

        eventsList.innerHTML = "";

        const dayEvents = eventsData[dateKey];

        if (dayEvents && dayEvents.length > 0) {
            dayEvents.forEach(evt => {
                const item = document.createElement("div");
                item.classList.add("event-item");

                const title = document.createElement("div");
                title.classList.add("event-item-title");
                title.textContent = evt.title;

                const time = document.createElement("div");
                time.classList.add("event-item-time");
                time.innerHTML = `<i class="fa-regular fa-clock"></i> ${evt.time}`;

                item.appendChild(title);
                item.appendChild(time);
                eventsList.appendChild(item);
            });
        } else {
            eventsList.innerHTML = `<p class="no-events-placeholder">No scheduled events for this date</p>`;
        }
    }

    // Month Navigation Controls
    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            currentViewDate.setMonth(currentViewDate.getMonth() - 1);

            // Re-select "Today" when returning to current month/year
            const viewYear = currentViewDate.getFullYear();
            const viewMonth = currentViewDate.getMonth();
            if (viewYear === today.getFullYear() && viewMonth === today.getMonth()) {
                selectedDateKey = realTodayKey;
            } else {
                selectedDateKey = null;
            }

            renderCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            currentViewDate.setMonth(currentViewDate.getMonth() + 1);

            // Re-select "Today" when returning to current month/year
            const viewYear = currentViewDate.getFullYear();
            const viewMonth = currentViewDate.getMonth();
            if (viewYear === today.getFullYear() && viewMonth === today.getMonth()) {
                selectedDateKey = realTodayKey;
            } else {
                selectedDateKey = null;
            }

            renderCalendar();
        });
    }

    // Initial Trigger
    renderCalendar();
});