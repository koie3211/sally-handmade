@extends('budget.layout')

@section('title', '行事曆')

@section('content')
<div x-data="calendarApp({{ json_encode($appointments) }}, {{ $year }}, {{ $month }})"
     x-init="init()"
     @click.self="selectedDate = null">

    {{-- 頂部：月份導覽 --}}
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 px-4 pb-4 pt-12 safe-area-top">
        <div class="flex items-center justify-between">
            <button @click="prevMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-indigo-200 hover:bg-indigo-700/50 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="text-center">
                <p class="text-xl font-bold text-white" x-text="`${currentYear} 年 ${currentMonth} 月`"></p>
                <p class="text-xs text-indigo-200 mt-0.5"
                   x-text="monthAppointmentCount + ' 個預約'"></p>
            </div>

            <button @click="nextMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-indigo-200 hover:bg-indigo-700/50 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- 星期列 --}}
        <div class="mt-4 grid grid-cols-7 text-center">
            <template x-for="w in ['日','一','二','三','四','五','六']" :key="w">
                <span class="text-xs font-medium text-indigo-200 py-1" x-text="w"></span>
            </template>
        </div>

        {{-- 日期格 --}}
        <div class="grid grid-cols-7">
            <template x-for="(day, i) in calendarDays" :key="i">
                <button
                    class="relative flex flex-col items-center py-1.5 transition"
                    :class="{
                        'opacity-0 pointer-events-none': !day,
                        'rounded-xl bg-white/20': day && selectedDate === day.date,
                    }"
                    @click="day && selectDate(day.date)">

                    <span class="text-sm font-medium leading-tight"
                          :class="{
                              'text-white': day,
                              'text-yellow-300 font-bold': day && day.isToday,
                              'text-white/40': day && (day.weekday === 0 || day.weekday === 6),
                          }"
                          x-text="day ? day.d : ''">
                    </span>

                    {{-- 預約指示點 --}}
                    <div class="flex gap-0.5 mt-0.5 h-1.5">
                        <template x-if="day && day.count > 0">
                            <div class="flex gap-0.5">
                                <span class="w-1 h-1 rounded-full bg-yellow-300"></span>
                                <template x-if="day.count > 1">
                                    <span class="w-1 h-1 rounded-full bg-yellow-300/60"></span>
                                </template>
                            </div>
                        </template>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- 選中日期的預約清單 --}}
    <div class="mx-4 mt-4">

        {{-- 未選日期：顯示本月所有預約 --}}
        <template x-if="!selectedDate">
            <div>
                <h2 class="mb-3 text-sm font-semibold text-slate-500 uppercase tracking-wide">
                    本月所有預約
                    <span class="ml-1 text-indigo-600" x-text="'(' + monthAppointmentCount + ')'"></span>
                </h2>

                <template x-if="appointments.length === 0">
                    <div class="rounded-2xl bg-white p-8 text-center text-slate-400 shadow-sm ring-1 ring-slate-100">
                        <p class="text-3xl">📅</p>
                        <p class="mt-2 text-sm">本月還沒有預約，點 + 新增</p>
                    </div>
                </template>

                <div class="space-y-2">
                    <template x-for="apt in appointments" :key="apt.id">
                        <div class="flex items-start gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-100"
                             @click="openEditSheet(apt)">
                            <div class="flex-shrink-0 rounded-xl bg-indigo-50 px-2 py-1 text-center min-w-[3rem]">
                                <p class="text-lg font-bold text-indigo-600 leading-none"
                                   x-text="apt.start_at.substring(8,10)"></p>
                                <p class="text-xs text-indigo-400"
                                   x-text="monthName(apt.start_at.substring(5,7))"></p>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800 truncate" x-text="apt.title"></p>
                                <p class="text-xs text-slate-400 mt-0.5"
                                   x-text="apt.start_time + (apt.end_time ? ' – ' + apt.end_time : '')"></p>
                                <p class="text-xs text-slate-400 truncate" x-text="apt.note" x-show="apt.note"></p>
                            </div>
                            <button class="flex-shrink-0 text-slate-300 hover:text-rose-400 transition p-1"
                                    @click.stop="confirmDelete(apt.id)">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- 選中日期：顯示當天預約 --}}
        <template x-if="selectedDate">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide"
                        x-text="formatSelectedDate()"></h2>
                    <button @click="selectedDate = null"
                            class="text-xs text-indigo-600 font-medium">返回全月</button>
                </div>

                <template x-if="dayAppointments.length === 0">
                    <div class="rounded-2xl bg-white p-6 text-center text-slate-400 shadow-sm ring-1 ring-slate-100">
                        <p class="text-sm">這天還沒有預約</p>
                        <button @click="openSheet(selectedDate)"
                                class="mt-2 text-sm text-indigo-600 font-medium">+ 新增預約</button>
                    </div>
                </template>

                <div class="space-y-2">
                    <template x-for="apt in dayAppointments" :key="apt.id">
                        <div class="flex items-start gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-100"
                             @click="openEditSheet(apt)">
                            <div class="flex-shrink-0 rounded-xl bg-indigo-50 px-2 py-1 text-center min-w-[3rem]">
                                <p class="text-sm font-bold text-indigo-600 leading-none" x-text="apt.start_time"></p>
                                <template x-if="apt.end_time">
                                    <p class="text-xs text-indigo-300 mt-0.5" x-text="apt.end_time"></p>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800 truncate" x-text="apt.title"></p>
                                <p class="text-xs text-slate-400 truncate mt-0.5" x-text="apt.note" x-show="apt.note"></p>
                            </div>
                            <button class="flex-shrink-0 text-slate-300 hover:text-rose-400 transition p-1"
                                    @click.stop="confirmDelete(apt.id)">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- 浮動新增按鈕 --}}
    <button @click="openSheet(selectedDate)"
            class="fixed right-5 z-50 flex h-14 w-14 items-center justify-center
                   rounded-full bg-indigo-600 text-white text-2xl shadow-lg shadow-indigo-500/40
                   transition hover:bg-indigo-700 active:scale-90"
            style="bottom: calc(5rem + env(safe-area-inset-bottom))">
        +
    </button>

    {{-- ── 刪除確認彈窗 ─────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="deletingId !== null"
             class="fixed inset-0 z-50 flex items-end justify-center bg-black/40 px-4 pb-8"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="deletingId = null">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-slate-800">確定刪除這筆預約？</h3>
                <p class="mt-1 text-sm text-slate-500">刪除後無法復原。</p>
                <div class="mt-5 flex gap-3">
                    <button @click="deletingId = null"
                            class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-medium text-slate-700">
                        取消
                    </button>
                    <button @click="performDelete()"
                            :disabled="deleting"
                            class="flex-1 rounded-xl bg-rose-500 py-2.5 text-sm font-bold text-white disabled:opacity-50">
                        <span x-text="deleting ? '刪除中…' : '確定刪除'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ── 新增/編輯預約 Bottom Sheet ─────────────────── --}}
    <template x-teleport="body">
        <div x-show="sheetOpen"
             class="fixed inset-0 z-50 flex items-end bg-black/40"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="closeSheet()">

            <div class="w-full rounded-t-3xl bg-white pb-safe shadow-2xl"
                 :style="{ transform: `translateY(${dragY > 0 ? dragY : 0}px)`, transition: dragging ? 'none' : 'transform 0.3s cubic-bezier(0.32,0.72,0,1)' }"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">

                {{-- 拖曳把手 --}}
                <div class="flex justify-center pt-3 pb-1 cursor-grab"
                     @touchstart.prevent="dragStart($event)"
                     @touchmove.prevent="dragMove($event)"
                     @touchend="dragEnd()">
                    <div class="h-1 w-10 rounded-full bg-slate-300"></div>
                </div>

                <div class="px-5 pb-2 pt-1">
                    <h2 class="text-lg font-bold text-slate-800"
                        x-text="editingId ? '編輯預約' : '新增預約'"></h2>
                </div>

                <form @submit.prevent="submitAppointment()" class="px-5 pb-8 space-y-4">

                    {{-- 標題 --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">標題</label>
                        <input type="text"
                               x-model="form.title"
                               placeholder="預約名稱"
                               maxlength="100"
                               required
                               class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    </div>

                    {{-- 開始時間 --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">開始時間</label>
                            <input type="datetime-local"
                                   x-model="form.start_at"
                                   required
                                   class="mt-1.5 w-full rounded-xl border border-slate-200 px-3 py-3 text-sm text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">結束時間</label>
                            <input type="datetime-local"
                                   x-model="form.end_at"
                                   :min="form.start_at"
                                   class="mt-1.5 w-full rounded-xl border border-slate-200 px-3 py-3 text-sm text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        </div>
                    </div>

                    {{-- 備註 --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">備註</label>
                        <textarea x-model="form.note"
                                  placeholder="選填"
                                  rows="2"
                                  maxlength="500"
                                  class="mt-1.5 w-full resize-none rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                    </div>

                    {{-- 錯誤訊息 --}}
                    <p x-show="errorMsg" x-text="errorMsg" class="text-sm text-rose-500"></p>

                    {{-- 送出 --}}
                    <button type="submit"
                            :disabled="submitting"
                            class="w-full rounded-2xl bg-indigo-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-700 active:scale-95 disabled:opacity-50">
                        <span x-text="submitting ? '儲存中…' : (editingId ? '儲存變更' : '新增預約')"></span>
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
function calendarApp(initialAppointments, initYear, initMonth) {
    return {
        currentYear:  initYear,
        currentMonth: initMonth,
        appointments: initialAppointments,
        selectedDate: null,
        calendarDays: [],

        // 刪除
        deletingId: null,
        deleting: false,

        // Sheet
        sheetOpen: false,
        editingId: null,
        submitting: false,
        errorMsg: '',
        form: { title: '', start_at: '', end_at: '', note: '' },

        // 拖曳收起
        dragging: false,
        dragY: 0,
        dragStartY: 0,

        init() {
            this.buildCalendar()
        },

        get monthAppointmentCount() {
            return this.appointments.length
        },

        get dayAppointments() {
            if (!this.selectedDate) return []
            return this.appointments.filter(a => a.start_date === this.selectedDate)
        },

        buildCalendar() {
            const days = []
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1)
            const lastDay  = new Date(this.currentYear, this.currentMonth, 0)
            const today    = new Date().toISOString().substring(0, 10)

            // 填補月初空格（週日=0為第一欄）
            for (let i = 0; i < firstDay.getDay(); i++) {
                days.push(null)
            }

            // 計算每天的預約數
            const countMap = {}
            this.appointments.forEach(a => {
                countMap[a.start_date] = (countMap[a.start_date] ?? 0) + 1
            })

            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date = `${this.currentYear}-${String(this.currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`
                const weekday = new Date(this.currentYear, this.currentMonth - 1, d).getDay()
                days.push({
                    d,
                    date,
                    weekday,
                    isToday: date === today,
                    count: countMap[date] ?? 0,
                })
            }

            this.calendarDays = days
        },

        selectDate(date) {
            this.selectedDate = this.selectedDate === date ? null : date
        },

        formatSelectedDate() {
            if (!this.selectedDate) return ''
            const d = new Date(this.selectedDate + 'T00:00:00')
            const weekNames = ['日','一','二','三','四','五','六']
            return `${d.getMonth() + 1} 月 ${d.getDate()} 日（${weekNames[d.getDay()]}）`
        },

        monthName(mm) {
            return parseInt(mm) + ' 月'
        },

        async prevMonth() {
            if (this.currentMonth === 1) {
                this.currentYear--
                this.currentMonth = 12
            } else {
                this.currentMonth--
            }
            this.selectedDate = null
            await this.fetchMonth()
        },

        async nextMonth() {
            if (this.currentMonth === 12) {
                this.currentYear++
                this.currentMonth = 1
            } else {
                this.currentMonth++
            }
            this.selectedDate = null
            await this.fetchMonth()
        },

        async fetchMonth() {
            try {
                const res = await fetch(`/api/calendar/monthly?year=${this.currentYear}&month=${this.currentMonth}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                const json = await res.json()
                this.appointments = json.data
                this.buildCalendar()
            } catch (e) {
                console.error(e)
            }
        },

        // ── Sheet ─────────────────────────────────────────

        openSheet(date = null) {
            this.editingId = null
            this.errorMsg = ''
            const defaultDate = date ?? this.selectedDate ?? new Date().toISOString().substring(0, 10)
            this.form = {
                title:    '',
                start_at: defaultDate + 'T09:00',
                end_at:   '',
                note:     '',
            }
            this.sheetOpen = true
            this.dragY = 0
        },

        openEditSheet(apt) {
            this.editingId = apt.id
            this.errorMsg = ''
            this.form = {
                title:    apt.title,
                start_at: apt.start_at,
                end_at:   apt.end_at ?? '',
                note:     apt.note ?? '',
            }
            this.sheetOpen = true
            this.dragY = 0
        },

        closeSheet() {
            this.sheetOpen = false
        },

        async submitAppointment() {
            this.errorMsg = ''
            this.submitting = true
            try {
                const body = {
                    title:    this.form.title,
                    start_at: this.form.start_at,
                    end_at:   this.form.end_at || null,
                    note:     this.form.note   || null,
                }

                const url    = this.editingId ? `/appointments/${this.editingId}` : '/appointments'
                const method = this.editingId ? 'PUT' : 'POST'

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                })

                if (!res.ok) {
                    const err = await res.json()
                    this.errorMsg = err.message ?? '發生錯誤，請稍後再試'
                    return
                }

                const json = await res.json()
                const apt  = json.data

                if (this.editingId) {
                    const idx = this.appointments.findIndex(a => a.id === this.editingId)
                    if (idx !== -1) this.appointments.splice(idx, 1, apt)
                } else {
                    this.appointments.push(apt)
                    this.appointments.sort((a, b) => a.start_at.localeCompare(b.start_at))
                }

                this.buildCalendar()
                this.closeSheet()
            } catch (e) {
                this.errorMsg = '網路錯誤，請稍後再試'
            } finally {
                this.submitting = false
            }
        },

        // ── 刪除 ──────────────────────────────────────────

        confirmDelete(id) {
            this.deletingId = id
        },

        async performDelete() {
            this.deleting = true
            try {
                const res = await fetch(`/appointments/${this.deletingId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })

                if (res.ok || res.status === 204) {
                    this.appointments = this.appointments.filter(a => a.id !== this.deletingId)
                    this.buildCalendar()
                    this.deletingId = null
                }
            } catch (e) {
                console.error(e)
            } finally {
                this.deleting = false
            }
        },

        // ── 拖曳收起 ──────────────────────────────────────

        dragStart(e) {
            this.dragging   = true
            this.dragStartY = e.touches[0].clientY
            this.dragY      = 0
        },

        dragMove(e) {
            if (!this.dragging) return
            const dy = e.touches[0].clientY - this.dragStartY
            this.dragY = Math.max(0, dy)
        },

        dragEnd() {
            this.dragging = false
            if (this.dragY > 100) {
                this.closeSheet()
            }
            this.dragY = 0
        },
    }
}
</script>
@endsection
