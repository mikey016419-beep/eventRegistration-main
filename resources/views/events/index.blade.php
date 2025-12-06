<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Event List') }}
            </h2>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('events.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Event
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 成功/错误消息 -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- 搜索和筛选表单 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- 搜索活动名称 -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                       placeholder="Search by event name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- 搜索地点 -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" id="location" name="location" value="{{ request('location') }}" 
                                       placeholder="Search by location"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- 开始时间 -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date (From)</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- 结束时间 -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date (To)</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- 状态筛选 -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All</option>
                                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Ended</option>
                                    <option value="full" {{ request('status') === 'full' ? 'selected' : '' }}>Full</option>
                                </select>
                            </div>

                            <!-- 排序方式 -->
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                                <select id="sort_by" name="sort_by" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="event_time" {{ request('sort_by', 'event_time') === 'event_time' ? 'selected' : '' }}>Event Time</option>
                                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Event Name</option>
                                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created At</option>
                                </select>
                            </div>

                            <!-- 排序顺序 -->
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                <select id="sort_order" name="sort_order" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="asc" {{ request('sort_order', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('events.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Reset
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 活动列表 -->
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                        {{ $event->name }}
                                    </a>
                                </h3>
                                
                                <div class="text-sm text-gray-600 space-y-1 mb-4">
                                    <p><strong>Event Time:</strong> {{ $event->event_time->format('Y-m-d H:i') }}</p>
                                    <p><strong>Location:</strong> {{ $event->location }}</p>
                                    <p><strong>Participants:</strong> {{ $event->current_participants }} / {{ $event->max_participants }}</p>
                                </div>

                                <!-- 活动状态标签 -->
                                @if($event->isEnded())
                                    <span class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">Registration Closed</span>
                                @elseif($event->isStarted())
                                    <span class="inline-block bg-yellow-200 text-yellow-800 text-xs px-2 py-1 rounded">Ongoing</span>
                                @elseif($event->isFull())
                                    <span class="inline-block bg-red-200 text-red-800 text-xs px-2 py-1 rounded">Full</span>
                                @else
                                    <span class="inline-block bg-green-200 text-green-800 text-xs px-2 py-1 rounded">Open for Registration</span>
                                @endif

                                <div class="mt-4">
                                    <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View Details →
                                    </a>
                                    <div class="text-xs text-gray-500 mt-2">
                                        <span>Note: Registration closes one day before the event starts.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 分页 -->
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p>No events available</p>
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('events.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    Create the first event
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>