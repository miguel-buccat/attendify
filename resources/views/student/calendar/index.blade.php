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
                <div id="calendar-container" class="bg-base-100 border border-base-300/50 rounded-2xl p-5">
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
                <div id="day-detail" class="hidden bg-base-100 border border-base-300/50 rounded-2xl p-5">
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
<x-layouts.app title="Attendance Calendar">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; }
        .calendar-day { min-height: 100px; padding: 10px; border: 1px solid #f3f4f6; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="attendance" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                
                <div class="d d1 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Overview</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Attendance Calendar</h1>
                        <p class="mt-1 text-sm text-base-content/50">Visualize your attendance performance by date.</p>
                    </div>
                </div>

                <div class="d d2 bg-base-100 rounded-3xl border border-base-300 shadow-sm overflow-hidden">
                    <div class="p-6">
                        @php
                            $now = now();
                            $daysInMonth = $now->daysInMonth;
                            $firstDayOfMonth = $now->copy()->startOfMonth()->dayOfWeek;
                            $monthName = $now->format('F Y');
                        @endphp
                        
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-xl font-bold">{{ $monthName }}</h2>
                            <div class="flex items-center gap-2">
                                <button class="btn btn-ghost btn-sm btn-circle opacity-30 cursor-not-allowed">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button class="btn btn-ghost btn-sm btn-circle opacity-30 cursor-not-allowed">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 border-t border-l border-base-300">
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                <div class="p-3 text-center border-b border-r border-base-300 text-[10px] font-bold uppercase tracking-widest text-base-content/40 bg-base-200/50">
                                    {{ $day }}
                                </div>
                            @endforeach

                            @for($i = 0; $i < $firstDayOfMonth; $i++)
                                <div class="calendar-day bg-base-200/20 border-b border-r border-base-300"></div>
                            @endfor

                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $dateStr = $now->copy()->day($day)->toDateString();
                                    $dayRecords = $records->filter(function($r) use ($dateStr) {
                                        return $r->created_at->toDateString() === $dateStr;
                                    });
                                @endphp
                                <div class="calendar-day border-b border-r border-base-300 hover:bg-base-200/30 transition-colors">
                                    <span class="text-xs font-bold {{ $now->day === $day ? 'size-6 rounded-full bg-primary text-white flex items-center justify-center -ml-1 -mt-1 shadow-md shadow-primary/20' : 'text-base-content/30' }}">
                                        {{ $day }}
                                    </span>
                                    <div class="mt-2 space-y-1">
                                        @foreach($dayRecords as $record)
                                            <div class="text-[9px] px-1.5 py-0.5 rounded border 
                                                {{ $record->status->label() === 'Present' ? 'bg-success/10 border-success/30 text-success font-bold' : ($record->status->label() === 'Late' ? 'bg-warning/10 border-warning/30 text-warning font-bold' : 'bg-error/10 border-error/30 text-error font-bold') }} truncate" title="{{ $record->classSession->schoolClass->name }}">
                                                {{ $record->classSession->schoolClass->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endfor
                            
                            {{-- Fill remaining grid slots --}}
                            @for($i = ($firstDayOfMonth + $daysInMonth); $i < (ceil(($firstDayOfMonth + $daysInMonth)/7)*7); $i++)
                                <div class="calendar-day bg-base-200/20 border-b border-r border-base-300"></div>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layouts.app>
