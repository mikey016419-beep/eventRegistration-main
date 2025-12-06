<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('events.update', $event) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- 标识来源，用于编辑后返回正确的页面 -->
                        @if(request('from') === 'manage')
                            <input type="hidden" name="from" value="manage">
                        @endif

                        <!-- 活动名称 -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Event Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required maxlength="50">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- 活动时间 -->
                        <div class="mb-4">
                            <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">Event Time *</label>
                            <input type="datetime-local" id="event_time" name="event_time" 
                                   value="{{ old('event_time', $event->event_time->format('Y-m-d\TH:i')) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required min="{{ date('Y-m-d\TH:i', strtotime('+1 day +1 minute')) }}">
                            <p class="mt-1 text-sm text-gray-500">Event time must be at least 1 day after the current time (at least the day after tomorrow), because registration closes one day before the event starts.</p>
                            <x-input-error :messages="$errors->get('event_time')" class="mt-2" />
                        </div>

                        <!-- 活动地点 -->
                        <div class="mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $event->location) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required maxlength="100">
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- 最大参与人数 -->
                        <div class="mb-4">
                            <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-1">Max Participants *</label>
                            <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required min="1">
                            <x-input-error :messages="$errors->get('max_participants')" class="mt-2" />
                        </div>

                        <!-- 活动简介 -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $event->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- 提交按钮 -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ request('from') === 'manage' ? route('events.manage') : route('events.show', $event) }}" 
                               class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>