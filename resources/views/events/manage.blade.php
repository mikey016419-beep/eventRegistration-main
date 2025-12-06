<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Event Management') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('events.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Event
                </a>
                <a href="{{ route('events.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Event List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- 
                Table width settings:
                1. Adjust page container width: modify max-w-7xl (options: max-w-4xl, max-w-5xl, max-w-6xl, max-w-7xl, max-w-full)
                2. Adjust table width: modify the table tag's class on line 120
                3. Adjust column width: modify <th> and <td> tags' class, add w-* classes (e.g., w-32, w-48, w-64)
            -->
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
                    <form method="GET" action="{{ route('events.manage') }}" class="space-y-4">
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
                                    <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('events.manage') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Reset
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 活动管理表格 -->
            @if($events->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($events as $event)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                                    {{ $event->name }}
                                                </a>
                                            </div>
                                            @if($event->description)
                                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                                    {{ Str::limit($event->description, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $event->creator->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $event->event_time->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $event->location }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="{{ $event->isFull() ? 'text-red-600 font-semibold' : '' }}">
                                                {{ $event->current_participants }} / {{ $event->max_participants }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($event->isEnded())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Registration Closed</span>
                                            @elseif($event->isStarted())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ongoing</span>
                                            @elseif($event->isFull())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Full</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Open for Registration</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- 查看详情 -->
                                                <a href="{{ route('events.show', $event) }}" 
                                                   class="text-blue-600 hover:text-blue-900" 
                                                   title="View Details">
                                                    View
                                                </a>
                                                <!-- 编辑按钮 -->
                                                @if($event->canEdit())
                                                    <a href="{{ route('events.edit', $event) }}?from=manage" 
                                                       class="text-green-600 hover:text-green-900"
                                                       title="Edit Event">
                                                        Edit
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed" title="Event has started, cannot edit">
                                                        Edit
                                                    </span>
                                                @endif
                                                <!-- 删除按钮 -->
                                                @if($event->canDelete())
                                                    <form action="{{ route('events.destroy', $event) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to delete event \"{{ $event->name }}\"? This action cannot be undone!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="from" value="manage">
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900"
                                                                title="Delete Event">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed" title="Event has started, cannot delete">
                                                        Delete
                                                    </span>
                                                @endif
                                                <!-- 导出报名数据 -->
                                                <a href="{{ route('events.export', $event) }}" 
                                                   class="text-purple-600 hover:text-purple-900"
                                                   title="Export Registration Data">
                                                    Export
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 分页 -->
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p>No events available</p>
                        <a href="{{ route('events.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Create the first event
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
