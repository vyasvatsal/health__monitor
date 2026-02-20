<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Settings Sidebar -->
                <aside class="w-full md:w-64 flex-shrink-0">
                    <div class="bg-[#1e293b] rounded-lg border border-white/10 overflow-hidden sticky top-24">
                        <div class="p-4 border-b border-white/5 bg-slate-900/50">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Configuration</h3>
                        </div>
                        <nav class="p-2 space-y-1">
                            @foreach([
                                    ['url' => route('settings.index'), 'icon' => 'tune', 'label' => 'General', 'route' => 'settings.index'],
                                    ['url' => route('settings.alerts'), 'icon' => 'notifications', 'label' => 'Alerts', 'route' => 'settings.alerts*'],
                                    ['url' => route('settings.developer'), 'icon' => 'code', 'label' => 'Developer', 'route' => 'settings.developer'],
                                    ['url' => route('profile.edit'), 'icon' => 'person', 'label' => 'Profile', 'route' => 'profile.edit'],
                                ] as $item)
                                    <a href="{{ $item['url'] }}" 
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs($item['route']) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                        <i class="material-icons text-[20px] {{ request()->routeIs($item['route']) ? 'text-emerald-500' : 'text-slate-500 group-hover:text-white' }}">{{ $item['icon'] }}</i>
                                        {{ $item['label'] }}
                                    </a>

                               @endforeach
                        </nav>
                   </div>

                   <!-- Help / Info Box -->
                   <div class="mt-6 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-lg border border-indigo-500/20 p-4 sticky top-[300px]">
                       <div class="flex items-start gap-3">
                            <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-400">

                                                                  <i class="material-icons text-xl">help_outline</i>
                             </div>
                             <div>
                                 <h4 class="text-sm font-bold text-white">Need Help?</h4>
                                 <p class="text-xs text-slate-400 mt-1 leading-relaxed">Check our documentation or contact support for assistance with configuration.</p>
                             </div>
                        </div>
                    </div>
                </aside>

                <!-- Content Area -->
                <div class="flex-1 min-w-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
