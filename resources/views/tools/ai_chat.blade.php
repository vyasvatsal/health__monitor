<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('AI Chat Playground') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-[#1e293b] overflow-hidden shadow-xl sm:rounded-lg border border-slate-700 h-[600px] flex flex-col">

                <!-- Chat Header -->
                <div class="p-4 border-b border-slate-700 bg-slate-800/50">
                    <h3 class="text-white font-semibold">Talk to Health Monitor AI</h3>
                    <p class="text-xs text-slate-400">Test your AI connection freely.</p>
                </div>

                <!-- Chat History -->
                <div id="chat-history" class="flex-1 overflow-y-auto p-4 space-y-4 bg-[#0f172a]">
                    <!-- Initial Greeting -->
                    <div class="flex justify-start">
                        <div class="bg-slate-700 text-slate-200 rounded-lg rounded-tl-none px-4 py-2 max-w-[80%]">
                            Hello! I'm your AI assistant. System is fully operational. How can I help you?
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-slate-700 bg-slate-800/50">
                    <form id="chat-form" class="flex gap-2">
                        <input type="text" id="user-input"
                            class="flex-1 bg-slate-900 border-slate-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Type a message..." required>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 flex items-center gap-2">
                            <span>Send</span>
                            <svg id="loading-icon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('chat-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const input = document.getElementById('user-input');
            const message = input.value.trim();
            if (!message) return;

            const history = document.getElementById('chat-history');
            const btn = this.querySelector('button');
            const loader = document.getElementById('loading-icon');

            // Add User Message
            history.innerHTML += `
                <div class="flex justify-end">
                    <div class="bg-indigo-600 text-white rounded-lg rounded-tr-none px-4 py-2 max-w-[80%]">
                        ${escapeHtml(message)}
                    </div>
                </div>
            `;

            input.value = '';
            btn.disabled = true;
            loader.classList.remove('hidden');
            scrollToBottom();

            try {
                const response = await fetch('{{ route('tools.ai-chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // Ensure we ask for JSON
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON Response:', text);
                    throw new Error('Server returned invalid response (See console for details)');
                }

                if (!response.ok) {
                    throw new Error(data.error || `HTTP Error ${response.status}`);
                }

                if (data.status === 'success') {
                    // Add AI Response
                    const formattedReply = (typeof marked !== 'undefined') ? marked.parse(data.reply) : escapeHtml(data.reply).replace(/\n/g, '<br>');

                    history.innerHTML += `
                        <div class="flex justify-start">
                            <div class="bg-slate-700 text-slate-200 rounded-lg rounded-tl-none px-4 py-2 max-w-[80%] prose prose-invert prose-sm">
                                ${formattedReply}
                            </div>
                        </div>
                    `;
                } else {
                    throw new Error(data.error || 'Unknown error');
                }

            } catch (err) {
                history.innerHTML += `
                    <div class="flex justify-center">
                        <div class="text-red-400 text-xs bg-red-900/20 px-3 py-1 rounded-full">
                            Error: ${err.message}
                        </div>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
                scrollToBottom();
            }
        });

        function scrollToBottom() {
            const history = document.getElementById('chat-history');
            history.scrollTop = history.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
    <!-- Add Marked.js for markdown rendering if not already present -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</x-app-layout>