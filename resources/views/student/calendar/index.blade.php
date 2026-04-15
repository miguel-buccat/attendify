<x-layouts.app title="Attendance Calendar">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="calendar" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div>
                    <h1 class="text-2xl font-black tracking-tight">Attendance Calendar</h1>
                    <p class="text-sm text-base-content/50 mt-1">Visual overview of your attendance history</p>
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap gap-4 text-xs">
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-full bg-success"></span> Present</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-full bg-warning"></span> Late</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-full bg-error"></span> Absent</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-full bg-info"></span> Excused</span>
                </div>

                {{-- Calendar Grid --}}
                <div id="calendar-container" class="af-card p-5">
                    <div class="flex items-center justify-between mb-5">
                        <button id="prev-month" type="button" class="btn btn-ghost btn-sm btn-square rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <h2 id="calendar-title" class="text-lg font-bold"></h2>
                        <button id="next-month" type="button" class="btn btn-ghost btn-sm btn-square rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-base-content/40 mb-2">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>

                    <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>
                </div>

                {{-- Day Detail --}}
                <div id="day-detail" class="hidden af-card p-5">
                    <h3 id="day-detail-title" class="font-bold text-sm mb-3"></h3>
                    <div id="day-detail-content" class="space-y-2"></div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const records = @json($records);
            let currentDate = new Date();

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            const statusColors = {
                'Present': 'bg-success',
                'Late': 'bg-warning',
                'Absent': 'bg-error',
                'Excused': 'bg-info',
            };

            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const today = new Date();

                document.getElementById('calendar-title').textContent = `${monthNames[month]} ${year}`;

                const grid = document.getElementById('calendar-grid');
                grid.innerHTML = '';

                // Empty cells before first day
                for (let i = 0; i < firstDay; i++) {
                    grid.appendChild(createEmptyCell());
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const dayRecords = records[dateStr] || [];
                    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                    const cell = document.createElement('div');
                    cell.className = `relative min-h-[3rem] p-1 rounded-xl border transition-all cursor-pointer hover:bg-base-200 ${isToday ? 'border-primary/50 bg-primary/5' : 'border-base-300/30'}`;

                    let dayLabel = `<span class="text-xs font-medium ${isToday ? 'text-primary font-bold' : 'text-base-content/60'}">${day}</span>`;

                    if (dayRecords.length > 0) {
                        let dots = '<div class="flex flex-wrap gap-0.5 mt-1 justify-center">';
                        dayRecords.forEach(r => {
                            dots += `<span class="size-2 rounded-full ${statusColors[r.status] || 'bg-base-300'}"></span>`;
                        });
                        dots += '</div>';
                        dayLabel += dots;
                    }

                    cell.innerHTML = dayLabel;

                    cell.addEventListener('click', () => showDayDetail(dateStr, dayRecords));

                    grid.appendChild(cell);
                }
            }

            function createEmptyCell() {
                const cell = document.createElement('div');
                cell.className = 'min-h-[3rem]';
                return cell;
            }

            function showDayDetail(dateStr, dayRecords) {
                const detail = document.getElementById('day-detail');
                const title = document.getElementById('day-detail-title');
                const content = document.getElementById('day-detail-content');

                const date = new Date(dateStr + 'T00:00:00');
                title.textContent = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

                if (dayRecords.length === 0) {
                    content.innerHTML = '<p class="text-sm text-base-content/40">No classes on this day.</p>';
                } else {
                    content.innerHTML = dayRecords.map(r => `
                        <div class="flex items-center gap-3 p-2 rounded-xl bg-base-200/50">
                            <span class="size-3 rounded-full ${statusColors[r.status] || 'bg-base-300'} shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">${r['class']}</p>
                                <p class="text-xs text-base-content/50">${r.time || ''}</p>
                            </div>
                            <span class="badge badge-sm ${r.status === 'Present' ? 'badge-success' : r.status === 'Late' ? 'badge-warning' : r.status === 'Absent' ? 'badge-error' : 'badge-info'} rounded-lg">${r.status}</span>
                        </div>
                    `).join('');
                }

                detail.classList.remove('hidden');
            }

            document.getElementById('prev-month').addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });

            document.getElementById('next-month').addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });

            renderCalendar();
        });
    </script>
</x-layouts.app>
