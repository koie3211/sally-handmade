@extends('budget.layout')

@section('title', '行事曆')

@section('content')
<div x-data="calendarApp({{ json_encode($appointments) }}, {{ json_encode($nextWeekProjected) }}, {{ $year }}, {{ $month }})"
     x-init="init()">

    {{-- 頂部：月份導覽（僅保留導覽，不含日期格）--}}
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 px-4 pb-4 safe-area-top-compact">
        <div class="flex items-center justify-between">
            <button @click="prevMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-indigo-200 hover:bg-indigo-700/50 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="text-center">
                <p class="text-xl font-bold text-white" x-text="`${currentYear} 年 ${currentMonth} 月`"></p>
                <p class="text-xs text-indigo-200 mt-0.5" x-text="monthAppointmentCount + ' 個預約'"></p>
            </div>

            <button @click="nextMonth()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-indigo-200 hover:bg-indigo-700/50 transition active:scale-90">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- 月曆主體（白底，整頁寬）--}}
    <div class="bg-white">

        {{-- 星期標題列 --}}
        <div class="grid grid-cols-7 border-b border-slate-100">
            <template x-for="(w, wi) in ['日','一','二','三','四','五','六']" :key="wi">
                <div class="py-2 text-center text-xs font-semibold"
                     :class="wi === 0 ? 'text-rose-400' : (wi === 6 ? 'text-slate-400' : 'text-slate-500')"
                     x-text="w">
                </div>
            </template>
        </div>

        {{-- 日期格 --}}
        <div class="grid grid-cols-7 divide-x divide-slate-100">
            <template x-for="(day, i) in calendarDays" :key="i">
                <div class="min-h-[5.5rem] border-b border-slate-100 px-0.5 pt-1 pb-0.5 transition-colors cursor-pointer"
                     :class="{
                         'opacity-0 pointer-events-none': !day,
                         'bg-indigo-50': day && selectedDate === day.date,
                         'bg-white': day && selectedDate !== day.date,
                     }"
                     @click="day && selectDate(day.date)">

                    {{-- 日期數字 --}}
                    <div class="flex justify-center mb-0.5">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold leading-none"
                              :class="{
                                  'bg-indigo-600 text-white': day && day.isToday,
                                  'text-rose-400': day && !day.isToday && day.weekday === 0,
                                  'text-slate-400': day && !day.isToday && day.weekday === 6,
                                  'text-slate-700': day && !day.isToday && day.weekday > 0 && day.weekday < 6,
                              }"
                              x-text="day ? day.d : ''">
                        </span>
                    </div>

                    {{-- 預約 chip（最多顯示 3 筆）--}}
                    <template x-if="day && day.events.length > 0">
                        <div class="space-y-0.5">
                            <template x-for="(ev, ei) in day.events.slice(0, 3)" :key="ev.id">
                                <div class="flex items-center gap-0.5 rounded px-1 py-0.5"
                                     :class="ev.is_projected ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700'"
                                     @click.stop="ev.is_projected ? openProjectedSheet(ev) : openEditSheet(ev)">
                                    <span class="w-1 h-1 flex-shrink-0 rounded-full"
                                          :class="ev.is_projected ? 'bg-amber-500' : 'bg-indigo-500'"></span>
                                    <span class="truncate text-[10px] font-medium leading-tight"
                                          :class="ev.is_projected ? 'text-amber-700' : ''"
                                          x-text="ev.is_projected ? '預計 · ' + ev.title : ev.title"></span>
                                </div>
                            </template>

                            {{-- +N 更多 --}}
                            <template x-if="day.count > 3">
                                <div class="px-1 text-[10px] font-medium text-indigo-500 leading-tight"
                                     x-text="'+' + (day.count - 3) + ' 更多'">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <p class="px-5 pt-3 text-xs text-slate-400">← 左滑編輯　右滑刪除 →</p>

    {{-- 選中日期清單面板 --}}
    <div x-show="selectedDate"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mx-4 mt-4 mb-2 pb-24">

        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-700" x-text="formatSelectedDate()"></h2>
            <div class="flex items-center gap-3">
                <button @click="openSheet(selectedDate)"
                        class="text-xs font-semibold text-indigo-600">+ 新增</button>
                <button @click="selectedDate = null; resetAllSwipe()"
                        class="text-xs text-slate-400">關閉</button>
            </div>
        </div>

        <template x-if="dayAppointments.length === 0">
            <div class="rounded-2xl bg-white p-5 text-center text-slate-400 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm">這天還沒有預約</p>
            </div>
        </template>

        <div class="space-y-2">
            <template x-for="apt in dayAppointments" :key="apt.id">
                <div class="relative overflow-hidden rounded-2xl shadow-sm ring-1 ring-slate-100"
                     style="min-height: 64px">
                    <div class="absolute inset-0 flex">
                        <button x-show="!apt.is_projected"
                                @click="confirmDelete(apt.id); resetSwipe(apt.id)"
                                class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-rose-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span class="text-xs font-semibold">刪除</span>
                        </button>
                        <div class="flex-1"></div>
                        <button @click="apt.is_projected ? openProjectedSheet(apt) : openEditSheet(apt); resetSwipe(apt.id)"
                                class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-indigo-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="text-xs font-semibold" x-text="apt.is_projected ? '建立' : '編輯'"></span>
                        </button>
                    </div>

                    <div class="relative flex min-h-[64px] items-start gap-3 bg-white px-4 py-3 select-none"
                         :style="{
                             transform: `translateX(${swipeX(apt.id)}px)`,
                             transition: swipeDragging(apt.id) ? 'none' : 'transform 0.25s cubic-bezier(0.25,0.46,0.45,0.94)'
                         }"
                         @touchstart="swipeStart(apt.id, $event)"
                         @touchmove="swipeMove(apt.id, $event)"
                         @touchend="swipeEnd(apt.id)"
                         @click="resetAllSwipe()">
                        <div class="flex-shrink-0 w-14 text-right">
                            <p class="text-xs font-semibold"
                               :class="apt.is_projected ? 'text-amber-600' : 'text-indigo-600'"
                               x-text="apt.start_time"></p>
                            <p class="text-xs text-slate-400" x-text="apt.end_time" x-show="apt.end_time"></p>
                        </div>
                        <div class="min-w-0 flex-1 border-l-2 pl-3"
                             :class="apt.is_projected ? 'border-amber-300' : 'border-indigo-300'">
                            <p class="text-sm font-semibold text-slate-800 truncate" x-text="apt.title"></p>
                            <p x-show="apt.is_projected" class="text-[10px] font-semibold text-amber-600">下週預計，可編輯後建立</p>
                            <p class="text-xs text-slate-400 truncate mt-0.5" x-text="apt.note" x-show="apt.note"></p>
                            <p x-show="apt.linked_transactions_count > 0"
                               class="mt-0.5 text-[10px] font-medium text-indigo-500"
                               x-text="linkedTransactionLabel(apt)">
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- 未選日期：本月清單 --}}
    <div x-show="!selectedDate" class="mx-4 mt-4 pb-24">
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
                <div class="relative overflow-hidden rounded-2xl shadow-sm ring-1 ring-slate-100"
                     style="min-height: 64px">
                    <div class="absolute inset-0 flex">
                        <button x-show="!apt.is_projected"
                                @click="confirmDelete(apt.id); resetSwipe(apt.id)"
                                class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-rose-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span class="text-xs font-semibold">刪除</span>
                        </button>
                        <div class="flex-1"></div>
                        <button @click="apt.is_projected ? openProjectedSheet(apt) : openEditSheet(apt); resetSwipe(apt.id)"
                                class="flex w-20 flex-shrink-0 flex-col items-center justify-center gap-1 bg-indigo-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="text-xs font-semibold" x-text="apt.is_projected ? '建立' : '編輯'"></span>
                        </button>
                    </div>

                    <div class="relative flex min-h-[64px] items-start gap-3 bg-white px-4 py-3 select-none"
                         :style="{
                             transform: `translateX(${swipeX(apt.id)}px)`,
                             transition: swipeDragging(apt.id) ? 'none' : 'transform 0.25s cubic-bezier(0.25,0.46,0.45,0.94)'
                         }"
                         @touchstart="swipeStart(apt.id, $event)"
                         @touchmove="swipeMove(apt.id, $event)"
                         @touchend="swipeEnd(apt.id)"
                         @click="resetAllSwipe()">
                        <div class="flex-shrink-0 rounded-xl px-2 py-1 text-center min-w-[3rem]"
                             :class="apt.is_projected ? 'bg-amber-50' : 'bg-indigo-50'">
                            <p class="text-lg font-bold leading-none"
                               :class="apt.is_projected ? 'text-amber-600' : 'text-indigo-600'"
                               x-text="apt.start_at.substring(8,10)"></p>
                            <p class="text-xs"
                               :class="apt.is_projected ? 'text-amber-400' : 'text-indigo-400'"
                               x-text="parseInt(apt.start_at.substring(5,7)) + ' 月'"></p>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800 truncate" x-text="apt.title"></p>
                            <p x-show="apt.is_projected" class="text-[10px] font-semibold text-amber-600">下週預計，可編輯後建立</p>
                            <p class="text-xs text-slate-400 mt-0.5"
                               x-text="apt.start_time + (apt.end_time ? ' – ' + apt.end_time : '')"></p>
                            <p class="text-xs text-slate-400 truncate" x-text="apt.note" x-show="apt.note"></p>
                            <p x-show="apt.linked_transactions_count > 0"
                               class="mt-0.5 text-[10px] font-medium text-indigo-500"
                               x-text="linkedTransactionLabel(apt)">
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- 下週預計預約 --}}
    <div x-show="nextWeekAppointments.length > 0" class="mx-4 mt-5 pb-24">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-amber-700">下週預計預約</h2>
            <span class="text-xs text-amber-500" x-text="nextWeekLabel()"></span>
        </div>

        <div class="space-y-2">
            <template x-for="apt in nextWeekAppointments" :key="'next-week-' + apt.id">
                <button type="button"
                        @click="openProjectedSheet(apt)"
                        class="flex w-full items-start gap-3 rounded-2xl border border-dashed border-amber-200 bg-amber-50/60 px-4 py-3 text-left">
                    <div class="min-w-[3.5rem] text-center">
                        <p class="text-xs font-semibold text-amber-600" x-text="formatProjectedDate(apt.start_date)"></p>
                        <p class="mt-1 text-sm font-bold text-amber-700" x-text="apt.start_time"></p>
                    </div>
                    <div class="min-w-0 flex-1 border-l-2 border-amber-300 pl-3">
                        <p class="truncate text-sm font-semibold text-slate-800" x-text="apt.title"></p>
                        <p class="mt-0.5 text-xs text-amber-700">預計聯繫 · 點擊後編輯並建立</p>
                        <p x-show="apt.note" class="mt-0.5 truncate text-xs text-slate-400" x-text="apt.note"></p>
                    </div>
                </button>
            </template>
        </div>
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

            <div class="w-full overflow-y-auto overscroll-contain rounded-t-3xl bg-white pb-safe shadow-2xl"
                 style="max-height: calc(100svh - env(safe-area-inset-top, 0px) - 0.5rem); -webkit-overflow-scrolling: touch;"
                 :style="{ transform: `translateY(${dragY > 0 ? dragY : 0}px)`, transition: dragging ? 'none' : 'transform 0.3s cubic-bezier(0.32,0.72,0,1)' }"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">

                <div class="sticky top-0 z-10 bg-white">
                    {{-- 拖曳把手 --}}
                    <div class="flex justify-center pt-3 pb-1 cursor-grab"
                         @touchstart.prevent="dragStart($event)"
                         @touchmove.prevent="dragMove($event)"
                         @touchend="dragEnd()">
                        <div class="h-1 w-10 rounded-full bg-slate-300"></div>
                    </div>

                    <div class="flex items-center justify-between px-5 pb-2 pt-1">
                        <h2 class="text-lg font-bold text-slate-800"
                            x-text="editingId ? '編輯預約' : '新增預約'"></h2>
                        <button type="button" @click="closeSheet()"
                                aria-label="關閉"
                                class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 active:scale-90">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
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
                               class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 text-base text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    </div>

                    {{-- 日期 --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">日期</label>
                        <div class="mt-1.5 flex items-center justify-between">
                            <button type="button" @click="shiftFormDate(-1)"
                                    class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition active:scale-90">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <p class="text-base font-semibold text-slate-800" x-text="formatFormDate()"></p>
                            <button type="button" @click="shiftFormDate(1)"
                                    class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition active:scale-90">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- 開始時間 --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">開始時間</label>
                        <div class="mt-1.5 grid grid-cols-4 gap-1.5 max-h-40 overflow-y-auto no-scrollbar">
                            <template x-for="slot in startTimeSlots" :key="'start-'+slot">
                                <button type="button"
                                        @click="selectStartTime(slot)"
                                        :data-start-selected="form.start_time === slot"
                                        :class="form.start_time === slot ? 'ring-2 ring-indigo-500 bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-600'"
                                        class="rounded-xl py-2 text-sm font-semibold transition active:scale-95"
                                        x-text="slot">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- 結束時間 --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">結束時間（選填）</label>
                        <div class="mt-1.5 grid grid-cols-4 gap-1.5 max-h-40 overflow-y-auto no-scrollbar">
                            <button type="button"
                                    @click="form.end_time = ''"
                                    :class="!form.end_time ? 'ring-2 ring-indigo-500 bg-indigo-50 text-indigo-700' : 'bg-slate-50 text-slate-500'"
                                    class="rounded-xl py-2 text-sm font-semibold transition active:scale-95">
                                不設定
                            </button>
                            <template x-for="slot in endTimeSlots" :key="'end-'+slot">
                                <button type="button"
                                        @click="selectEndTime(slot)"
                                        :disabled="isEndDisabled(slot)"
                                        :class="form.end_time === slot
                                            ? 'ring-2 ring-indigo-500 bg-indigo-50 text-indigo-700'
                                            : (isEndDisabled(slot) ? 'bg-slate-50 text-slate-300' : 'bg-slate-50 text-slate-600')"
                                        class="rounded-xl py-2 text-sm font-semibold transition active:scale-95 disabled:cursor-not-allowed"
                                        x-text="slot">
                                </button>
                            </template>
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
                            :disabled="submitting || !form.date || !form.start_time"
                            class="w-full rounded-2xl bg-indigo-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-700 active:scale-95 disabled:opacity-50">
                        <span x-text="submitting ? '儲存中…' : (editingId ? '儲存變更' : '新增預約')"></span>
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
function calendarApp(initialAppointments, initialNextWeekAppointments, initYear, initMonth) {
    return {
        currentYear:  initYear,
        currentMonth: initMonth,
        appointments: initialAppointments,
        nextWeekAppointments: initialNextWeekAppointments,
        selectedDate: null,
        calendarDays: [],
        swipe: {},

        // 刪除
        deletingId: null,
        deleting: false,

        // Sheet
        sheetOpen: false,
        editingId: null,
        submitting: false,
        errorMsg: '',
        form: { title: '', date: '', start_time: '09:00', end_time: '', note: '' },
        timeSlots: [],

        // 拖曳收起
        dragging: false,
        dragY: 0,
        dragStartY: 0,

        init() {
            const slots = []
            for (let total = 8 * 60; total <= 20 * 60; total += 5) {
                const h = Math.floor(total / 60)
                const m = total % 60
                slots.push(`${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`)
            }
            this.timeSlots = slots
            this.buildCalendar()
        },

        get monthAppointmentCount() {
            return this.appointments.length
        },

        get dayAppointments() {
            if (!this.selectedDate) return []
            return this.displayedAppointments.filter(a => a.start_date === this.selectedDate)
        },

        get displayedAppointments() {
            return [
                ...this.appointments,
                ...this.nextWeekAppointments.filter(a =>
                    a.start_date.startsWith(`${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}`)
                    && !this.appointments.some(existing => existing.id === a.id)
                ),
            ]
        },

        linkedTransactionLabel(appointment) {
            const parts = [`已連結 ${appointment.linked_transactions_count} 筆帳目`]
            if (appointment.linked_income > 0) {
                parts.push(`收入 +${Number(appointment.linked_income).toLocaleString('zh-TW')}`)
            }
            if (appointment.linked_expense > 0) {
                parts.push(`支出 -${Number(appointment.linked_expense).toLocaleString('zh-TW')}`)
            }
            return parts.join(' · ')
        },

        swipeX(id) {
            return this.swipe[id]?.x ?? 0
        },

        swipeDragging(id) {
            return this.swipe[id]?.dragging ?? false
        },

        swipeStart(id, event) {
            const touch = event.touches ? event.touches[0] : event
            this.swipe[id] = {
                x: 0,
                startX: touch.clientX,
                startY: touch.clientY,
                dragging: false,
                locked: null,
            }
        },

        swipeMove(id, event) {
            const state = this.swipe[id]
            if (!state) return

            const touch = event.touches ? event.touches[0] : event
            const dx = touch.clientX - state.startX
            const dy = touch.clientY - state.startY

            if (!state.locked) {
                if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return
                state.locked = Math.abs(dx) > Math.abs(dy) ? 'horizontal' : 'vertical'
            }

            if (state.locked === 'vertical') return

            event.preventDefault()
            state.dragging = true
            state.x = Math.max(-90, Math.min(90, dx))
        },

        swipeEnd(id) {
            const state = this.swipe[id]
            if (!state) return

            state.dragging = false
            if (state.locked === 'vertical') {
                state.x = 0
                return
            }

            if (state.x < -60) {
                state.x = -80
            } else if (state.x > 60) {
                state.x = 80
            } else {
                state.x = 0
            }
        },

        resetSwipe(id) {
            if (this.swipe[id]) this.swipe[id].x = 0
        },

        resetAllSwipe() {
            Object.keys(this.swipe).forEach(id => {
                this.swipe[id].x = 0
            })
        },

        get startTimeSlots() {
            return this.mergeSlot(this.timeSlots, this.form.start_time)
        },

        get endTimeSlots() {
            return this.mergeSlot(this.timeSlots, this.form.end_time)
        },

        buildCalendar() {
            const days     = []
            const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1)
            const lastDay  = new Date(this.currentYear, this.currentMonth, 0)
            const today    = new Date().toISOString().substring(0, 10)

            // 填補月初空格（週日=0 為第一欄）
            for (let i = 0; i < firstDay.getDay(); i++) {
                days.push(null)
            }

            // 建立日期 → 預約 map
            const dayMap = {}
            this.displayedAppointments.forEach(a => {
                if (!dayMap[a.start_date]) dayMap[a.start_date] = []
                dayMap[a.start_date].push(a)
            })

            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date    = `${this.currentYear}-${String(this.currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`
                const weekday = new Date(this.currentYear, this.currentMonth - 1, d).getDay()
                const all     = dayMap[date] ?? []
                days.push({
                    d,
                    date,
                    weekday,
                    isToday: date === today,
                    events:  all.slice(0, 4),   // 最多存 4 筆（3 顯示 + 1 判斷是否有「更多」）
                    count:   all.length,
                })
            }

            this.calendarDays = days
        },

        selectDate(date) {
            this.resetAllSwipe()
            this.selectedDate = this.selectedDate === date ? null : date
        },

        formatSelectedDate() {
            if (!this.selectedDate) return ''
            const d         = new Date(this.selectedDate + 'T00:00:00')
            const weekNames = ['日','一','二','三','四','五','六']
            return `${d.getMonth() + 1} 月 ${d.getDate()} 日（${weekNames[d.getDay()]}）`
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
                this.nextWeekAppointments = json.next_week_projected ?? []
                this.buildCalendar()
            } catch (e) {
                console.error(e)
            }
        },

        // ── Sheet ─────────────────────────────────────────

        mergeSlot(slots, extra) {
            if (!extra || slots.includes(extra)) return slots
            return [...slots, extra].sort()
        },

        toDateStr(d) {
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
        },

        splitDateTime(value) {
            if (!value) return { date: '', time: '' }
            const [date, time = ''] = value.split('T')
            return { date: date ?? '', time: time.slice(0, 5) }
        },

        composeDateTime(date, time) {
            if (!date || !time) return ''
            return `${date}T${time}`
        },

        formatFormDate() {
            if (!this.form.date) return ''
            const d = new Date(this.form.date + 'T00:00:00')
            const weekNames = ['日', '一', '二', '三', '四', '五', '六']
            return `${d.getMonth() + 1} 月 ${d.getDate()} 日（${weekNames[d.getDay()]}）`
        },

        nextWeekLabel() {
            if (!this.nextWeekAppointments.length) return ''
            const dates = this.nextWeekAppointments.map(a => a.start_date).sort()
            return `${this.formatProjectedDate(dates[0])}－${this.formatProjectedDate(dates[dates.length - 1])}`
        },

        formatProjectedDate(date) {
            const d = new Date(date + 'T00:00:00')
            return `${d.getMonth() + 1}/${d.getDate()}`
        },

        shiftFormDate(days) {
            const d = new Date(this.form.date + 'T00:00:00')
            d.setDate(d.getDate() + days)
            this.form.date = this.toDateStr(d)
        },

        addThirty(time) {
            const [h, m] = time.split(':').map(Number)
            const total = h * 60 + m + 30
            if (total > 20 * 60) return ''
            return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`
        },

        selectStartTime(time) {
            this.form.start_time = time
            if (!this.form.end_time) {
                this.form.end_time = this.addThirty(time)
            } else if (this.form.end_time < time) {
                this.form.end_time = this.addThirty(time)
            }
        },

        selectEndTime(time) {
            if (this.isEndDisabled(time)) return
            this.form.end_time = this.form.end_time === time ? '' : time
        },

        isEndDisabled(time) {
            return Boolean(this.form.start_time && time < this.form.start_time)
        },

        scrollTimeIntoView() {
            this.$nextTick(() => {
                document.querySelector('[data-start-selected="true"]')
                    ?.scrollIntoView({ block: 'nearest', inline: 'nearest' })
            })
        },

        openSheet(date = null) {
            this.editingId = null
            this.errorMsg  = ''
            const defaultDate = date ?? this.selectedDate ?? this.toDateStr(new Date())
            this.form = {
                title:      '',
                date:       defaultDate,
                start_time: '09:00',
                end_time:   '09:30',
                note:       '',
            }
            this.sheetOpen = true
            this.dragY     = 0
            this.scrollTimeIntoView()
        },

        openEditSheet(apt) {
            this.editingId = apt.id
            this.errorMsg  = ''
            const start = this.splitDateTime(apt.start_at)
            const end   = this.splitDateTime(apt.end_at)
            this.form = {
                title:      apt.title,
                date:       start.date || apt.start_date || this.toDateStr(new Date()),
                start_time: start.time || apt.start_time || '09:00',
                end_time:   end.time || apt.end_time || '',
                note:       apt.note ?? '',
            }
            this.sheetOpen = true
            this.dragY     = 0
            this.scrollTimeIntoView()
        },

        openProjectedSheet(apt) {
            this.editingId = null
            this.errorMsg = ''
            const start = this.splitDateTime(apt.start_at)
            const end = this.splitDateTime(apt.end_at)
            this.form = {
                title: apt.title,
                date: start.date || apt.start_date || this.toDateStr(new Date()),
                start_time: start.time || apt.start_time || '09:00',
                end_time: end.time || apt.end_time || '',
                note: apt.note ?? '',
            }
            this.sheetOpen = true
            this.dragY = 0
            this.scrollTimeIntoView()
        },

        closeSheet() {
            this.sheetOpen = false
        },

        async submitAppointment() {
            this.errorMsg  = ''
            if (!this.form.date || !this.form.start_time) {
                this.errorMsg = '請選擇日期與開始時間'
                return
            }
            this.submitting = true
            try {
                const body = {
                    title:    this.form.title,
                    start_at: this.composeDateTime(this.form.date, this.form.start_time),
                    end_at:   this.form.end_time
                        ? this.composeDateTime(this.form.date, this.form.end_time)
                        : null,
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
                    const err  = await res.json()
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
            const dy   = e.touches[0].clientY - this.dragStartY
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
