<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->name }}
            </h2>
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="space-x-2">
                        @if($event->canEdit())
                            <a href="{{ route('events.edit', $event) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Edit
                            </a>
                        @endif
                        @if($event->canDelete())
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Delete
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('events.export', $event) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Export Registrations
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- 成功/错误消息 -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <!-- 活动基本信息 -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $event->name }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                            <div>
                                <p class="font-semibold">Event Time</p>
                                <p>{{ $event->event_time->format('Y-m-d H:i') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold">Location</p>
                                <p>{{ $event->location }}</p>
                            </div>
                            <div>
                                <p class="font-semibold">Participants</p>
                                <p>{{ $event->current_participants }} / {{ $event->max_participants }}</p>
                            </div>
                            <div>
                                <p class="font-semibold">Creator</p>
                                <p>{{ $event->creator->name }}</p>
                            </div>
                        </div>

                        @if($event->description)
                            <div class="mt-4">
                                <p class="font-semibold mb-2">Description</p>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $event->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- 报名区域 -->
                    @auth
                        @if($userRegistration)
                            <div class="border-t pt-4">
                                <p class="text-green-600 mb-2">✓ You have registered for this event</p>
                                <p class="text-sm text-gray-600 mb-4">Contact Phone: {{ $userRegistration->phone }}</p>
                                @if($userRegistration->canCancel())
                                    <form action="{{ route('registrations.cancel', $userRegistration) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your registration?')">
                                        @csrf
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            @if($event->canRegister())
                                <div class="border-t pt-4">
                                    <h4 class="text-lg font-semibold mb-4">Register for Event</h4>
                                    <form action="{{ route('registrations.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        
                                        <div class="mb-4">
                                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone *</label>
                                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                   placeholder="Enter 11-digit phone number" required pattern="^1[3-9]\d{9}$">
                                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                            <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                        </div>

                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Register Now
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="border-t pt-4">
                                    @if($event->isStarted())
                                        <p class="text-red-600">Event has started, registration is closed</p>
                                    @elseif($event->isFull())
                                        <p class="text-red-600">Event is full, registration is closed</p>
                                    @endif
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="border-t pt-4">
                            <p class="text-gray-600 mb-2">Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">log in</a> or <a href="{{ route('register') }}" class="text-blue-600 hover:underline">register</a> to register for events</p>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- 返回列表 -->
            <div class="mt-4">
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Event List
                </a>
            </div>
        </div>
    </div>
</x-app-layout>