<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Image Compression Tool') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Upload Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600"
                            id="drop-zone">
                            <h3 class="text-lg font-medium mb-4">Upload Image</h3>

                            <form id="compression-form" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        for="file_input">Choose file</label>
                                    <input
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                        id="file_input" name="image" type="file" accept="image/*">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_help">SVG, PNG,
                                        JPG or GIF (MAX. 5MB).</p>
                                </div>

                                <div class="mb-4">
                                    <label for="quality"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quality
                                        (10-100)</label>
                                    <input type="range" id="quality" name="quality" min="10" max="100" value="80"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                                    <span id="quality-value" class="text-sm">80</span>%
                                </div>

                                <button type="submit" id="compress-btn"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 w-full">
                                    Compress Image
                                </button>
                            </form>

                            <div id="loading" class="hidden mt-4 text-center">
                                <svg aria-hidden="true" role="status"
                                    class="inline w-8 h-8 mr-3 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill" />
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>

                            <div id="error-message"
                                class="hidden mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                                role="alert"></div>

                        </div>

                        <!-- Result Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600 hidden"
                            id="result-section">
                            <h3 class="text-lg font-medium mb-4">Compression Result</h3>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-bold text-gray-500">Original</p>
                                    <p class="text-lg font-semibold" id="original-size">-</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-500">Compressed</p>
                                    <p class="text-lg font-semibold text-green-600" id="compressed-size">-</p>
                                </div>
                            </div>

                            <div
                                class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 mb-4 inline-block">
                                Saved: <span id="saved-bytes">-</span> (<span id="saved-percent">-</span>)
                            </div>

                            <div class="mb-4">
                                <img id="compressed-image" src="" alt="Compressed preview"
                                    class="max-w-full h-auto rounded border border-gray-300">
                            </div>

                            <a id="download-link" href="#" download="compressed-image.jpg"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 w-full block text-center">
                                Download Image
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('compression-form');
            const fileInput = document.getElementById('file_input');
            const qualityInput = document.getElementById('quality');
            const qualityValue = document.getElementById('quality-value');
            const resultSection = document.getElementById('result-section');
            const loading = document.getElementById('loading');
            const compressBtn = document.getElementById('compress-btn');
            const errorMessage = document.getElementById('error-message');

            // Quality slider update
            qualityInput.addEventListener('input', function () {
                qualityValue.textContent = this.value;
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (!fileInput.files.length) {
                    alert('Please select a file');
                    return;
                }

                const formData = new FormData(form);

                // UI State: Loading
                loading.classList.remove('hidden');
                compressBtn.disabled = true;
                compressBtn.classList.add('opacity-50', 'cursor-not-allowed');
                errorMessage.classList.add('hidden');
                resultSection.classList.add('hidden');

                fetch('{{ route('tools.compression.run') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI with results
                            document.getElementById('original-size').textContent = data.original_size;
                            document.getElementById('compressed-size').textContent = data.compressed_size;
                            document.getElementById('saved-bytes').textContent = data.saved_bytes;
                            document.getElementById('saved-percent').textContent = data.saved_percent;

                            const img = document.getElementById('compressed-image');
                            img.src = data.image_base64;

                            const link = document.getElementById('download-link');
                            link.href = data.image_base64;
                            // Set download filename based on mime or default
                            link.download = 'compressed.' + (data.mime_type.split('/')[1] || 'jpg');

                            resultSection.classList.remove('hidden');
                        } else {
                            throw new Error(data.message || 'Compression failed');
                        }
                    })
                    .catch(error => {
                        errorMessage.textContent = error.message;
                        errorMessage.classList.remove('hidden');
                    })
                    .finally(() => {
                        loading.classList.add('hidden');
                        compressBtn.disabled = false;
                        compressBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    });
            });
        });
    </script>
</x-app-layout>