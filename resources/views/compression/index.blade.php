<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Image Compression Tool') }}
        </h2>
    </x-slot>

    <!-- Custom CSS for Comparison Slider -->
    <style>
        .img-comp-container {
            position: relative;
            height: 400px;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .img-comp-img {
            position: absolute;
            width: auto;
            height: auto;
            overflow: hidden;
        }

        .img-comp-img img {
            display: block;
            max-width: none;
            height: 100%;
            object-fit: cover;
        }

        .img-comp-slider {
            position: absolute;
            z-index: 9;
            cursor: ew-resize;
            width: 40px;
            height: 40px;
            background-color: white;
            opacity: 0.9;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transition: all 0.2s ease;
        }

        .img-comp-slider::before {
            content: '↔';
            font-weight: bold;
            color: #333;
        }

        .img-comp-slider:hover {
            transform: translate(-50%, -50%) scale(1.1);
            background-color: #f8fafc;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row gap-8">

                        <!-- Left Panel: Controls -->
                        <div class="w-full md:w-1/3 space-y-6">
                            <div class="text-center md:text-left">
                                <h3
                                    class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-teal-400">
                                    Compress & Optimize
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Reduce file size without losing quality. Perfect for web optimization.
                                </p>
                            </div>

                            <form id="compression-form" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <!-- File Input -->
                                <div class="relative group">
                                    <label for="file_input"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50/50 dark:hover:bg-gray-800/80 dark:bg-gray-700/30 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition-all duration-300">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-4 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 transition-colors"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                    class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF
                                                (MAX. 5MB)</p>
                                            <p id="file-name"
                                                class="mt-2 text-sm text-blue-500 font-medium truncate max-w-[200px]">
                                            </p>
                                        </div>
                                        <input id="file_input" name="image" type="file" class="hidden"
                                            accept="image/*" />
                                    </label>
                                </div>

                                <!-- Quality Slider -->
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label for="quality"
                                            class="text-sm font-medium text-gray-900 dark:text-white">Quality</label>
                                        <span class="text-sm font-medium text-blue-500"><span
                                                id="quality-value">80</span>%</span>
                                    </div>
                                    <input type="range" id="quality" name="quality" min="10" max="100" value="80"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                                </div>

                                <button type="submit" id="compress-btn"
                                    class="w-full text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-3.5 text-center transition-all shadow-lg hover:shadow-blue-500/30 disabled:opacity-70 disabled:cursor-not-allowed">
                                    Compress Image
                                </button>
                            </form>

                            <div id="loading" class="hidden text-center py-4">
                                <div class="inline-flex items-center gap-2 text-blue-500 font-medium animate-pulse">
                                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Optimizing...
                                </div>
                            </div>

                            <div id="error-message"
                                class="hidden p-4 text-sm text-red-800 rounded-xl bg-red-50 dark:bg-red-900/20 dark:text-red-400 border border-red-200 dark:border-red-800"
                                role="alert"></div>

                            <!-- Stats Card (Initially Hidden) -->
                            <div id="stats-card" class="hidden glass-panel p-4 rounded-xl space-y-3">
                                <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    Results</h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <span class="text-xs text-gray-500">Original</span>
                                        <div id="original-size" class="text-lg font-semibold">-</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">Compressed</span>
                                        <div id="compressed-size" class="text-lg font-bold text-green-500">-</div>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700 mt-2">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"
                                        id="compression-bar"></div>
                                </div>
                                <div
                                    class="text-xs text-center text-green-600 dark:text-green-400 font-medium bg-green-100 dark:bg-green-900/30 py-1 rounded-lg">
                                    Saved <span id="saved-bytes">-</span> (<span id="saved-percent">-</span>)
                                </div>

                                <a id="download-link" href="#"
                                    class="flex items-center justify-center gap-2 w-full text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-4 py-2.5 transition-all shadow-lg shadow-green-500/20 mt-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>

                        </div>

                        <!-- Right Panel: Comparison View -->
                        <div class="w-full md:w-2/3">
                            <div
                                class="bg-gray-100 dark:bg-gray-900/50 rounded-2xl h-[500px] flex items-center justify-center border border-gray-200 dark:border-gray-700 relative overflow-hidden group">

                                <!-- Placeholder State -->
                                <div id="placeholder-state" class="text-center p-8 transition-opacity duration-300">
                                    <div
                                        class="w-20 h-20 bg-gray-200 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">Upload an image to see the comparison
                                    </p>
                                </div>

                                <!-- Comparison Container (Hidden initially) -->
                                <div id="comparison-container" class="img-comp-container hidden w-full h-full">
                                    <div class="img-comp-img">
                                        <img id="img-after" src="" alt="Compressed">
                                    </div>
                                    <div class="img-comp-img img-comp-overlay">
                                        <img id="img-before" src="" alt="Original">
                                    </div>
                                </div>

                                <!-- Labels (Overlay) -->
                                <div id="comparison-labels"
                                    class="absolute top-4 left-4 right-4 flex justify-between pointer-events-none opacity-0 transition-opacity duration-300">
                                    <span
                                        class="bg-black/50 backdrop-blur text-white text-xs px-2 py-1 rounded">Original</span>
                                    <span
                                        class="bg-green-500/80 backdrop-blur text-white text-xs px-2 py-1 rounded">Compressed</span>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Store original file URL to revoke later
        let originalFileUrl = null;

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('compression-form');
            const fileInput = document.getElementById('file_input');
            const qualityInput = document.getElementById('quality');
            const qualityValue = document.getElementById('quality-value');
            const loading = document.getElementById('loading');
            const compressBtn = document.getElementById('compress-btn');
            const errorMessage = document.getElementById('error-message');
            const fileNameDisplay = document.getElementById('file-name');

            // Result Elements
            const statsCard = document.getElementById('stats-card');
            const placeholderState = document.getElementById('placeholder-state');
            const comparisonContainer = document.getElementById('comparison-container');
            const comparisonLabels = document.getElementById('comparison-labels');
            const imgBefore = document.getElementById('img-before');
            const imgAfter = document.getElementById('img-after');

            // Quality slider
            qualityInput.addEventListener('input', function () {
                qualityValue.textContent = this.value;
            });

            // File selection
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    fileNameDisplay.textContent = file.name;

                    // Create preview of original
                    if (originalFileUrl) URL.revokeObjectURL(originalFileUrl);
                    originalFileUrl = URL.createObjectURL(file);

                    // Show preview immediately? Or wait for compress?
                    // Let's just show filename for now to keep it clean.
                }
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (!fileInput.files.length) {
                    alert('Please select a file');
                    return;
                }

                const file = fileInput.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (file.size > maxSize) {
                    errorMessage.innerHTML = `<span class="font-bold">Error:</span> File is too large. Maximum size is 5MB.`;
                    errorMessage.classList.remove('hidden');
                    return;
                }

                const formData = new FormData(form);

                // UI Loading State
                loading.classList.remove('hidden');
                compressBtn.disabled = true;
                errorMessage.classList.add('hidden');

                // Hide previous results
                statsCard.classList.add('hidden');
                comparisonContainer.classList.add('hidden');
                placeholderState.classList.remove('hidden');
                comparisonLabels.classList.remove('opacity-100');

                fetch('{{ route('tools.compression.run') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json' // Force JSON response expectation
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw new Error(data.message || 'Server Error');
                            }
                            return data;
                        });
                    } else {
                        const text = await response.text();
                        // Try to extract a meaningful error message from HTML if possible, or just generic
                        console.error("Non-JSON response:", text);
                        throw new Error("Server returned an invalid response. Check console for details.");
                    }
                })
                .then(data => {
                    if(data.success) {
                        displayResults(data);
                    } else {
                        throw new Error(data.message || 'Compression failed');
                    }
                })
                .catch(error => {
                    errorMessage.innerHTML = `<span class="font-bold">Error:</span> ${error.message}`;
                    errorMessage.classList.remove('hidden');
                })
                .finally(() => {
                    loading.classList.add('hidden');
                    compressBtn.disabled = false;
                });
            });
            function displayResults(data) {
                // Update Stats
                document.getElementById('original-size').textContent = data.original_size;
                document.getElementById('compressed-size').textContent = data.compressed_size;
                document.getElementById('saved-bytes').textContent = data.saved_bytes;
                document.getElementById('saved-percent').textContent = data.saved_percent;

                // Download Link
                const link = document.getElementById('download-link');
                link.href = data.download_url;
                link.download = 'compressed.' + (data.mime_type.split('/')[1] || 'jpg');

                // Images
                imgBefore.src = originalFileUrl;
                imgAfter.src = data.image_base64;

                // Show UI
                statsCard.classList.remove('hidden');
                placeholderState.classList.add('hidden');
                comparisonContainer.classList.remove('hidden');

                // Initialize Comparison Slider when images load
                imgAfter.onload = function () {
                    initComparisons();
                    comparisonLabels.classList.add('opacity-100');
                };
            }

            function initComparisons() {
                const x = comparisonContainer.getElementsByClassName("img-comp-overlay");
                // Remove existing sliders if any
                const existingSliders = document.getElementsByClassName("img-comp-slider");
                while (existingSliders.length > 0) {
                    existingSliders[0].parentNode.removeChild(existingSliders[0]);
                }

                for (let i = 0; i < x.length; i++) {
                    compareImages(x[i]);
                }
            }

            function compareImages(img) {
                let slider, clicked = 0, w, h;

                w = img.offsetWidth;
                h = img.offsetHeight;

                img.style.width = (w / 2) + "px";

                slider = document.createElement("DIV");
                slider.setAttribute("class", "img-comp-slider");
                img.parentElement.insertBefore(slider, img);

                slider.style.top = (h / 2) + "px";
                slider.style.left = (w / 2) - (slider.offsetWidth / 2) + "px";

                slider.addEventListener("mousedown", slideReady);
                window.addEventListener("mouseup", slideFinish);
                slider.addEventListener("touchstart", slideReady);
                window.addEventListener("touchend", slideFinish);

                function slideReady(e) {
                    e.preventDefault();
                    clicked = 1;
                    window.addEventListener("mousemove", slideMove);
                    window.addEventListener("touchmove", slideMove);
                }

                function slideFinish() {
                    clicked = 0;
                }

                function slideMove(e) {
                    let pos;
                    if (clicked == 0) return false;
                    pos = getCursorPos(e);
                    if (pos < 0) pos = 0;
                    if (pos > w) pos = w;
                    slide(pos);
                }

                function getCursorPos(e) {
                    let a, x = 0;
                    e = (e.changedTouches) ? e.changedTouches[0] : e;
                    a = img.getBoundingClientRect();
                    x = e.pageX - a.left;
                    x = x - window.pageXOffset;
                    return x;
                }

                function slide(x) {
                    img.style.width = x + "px";
                    slider.style.left = img.offsetWidth - (slider.offsetWidth / 2) + "px";
                }
            }
        });
    </script>
</x-app-layout>