/**
 * SBBU Attendance Management System - Face Recognition Logic
 * Optimized for reliability, error recovery, and clear status reporting.
 */

const FaceLogic = {
    modelsLoaded: false,
    isInitializing: false,
    _initPromise: null,
    status: 'loading',
    db: null,
    dbName: 'SBBU_FaceDB',
    storeName: 'studentFaces',

    // Using a more reliable CDN for face-api weights
    modelUrl: 'https://justadudewhohacks.github.io/face-api.js/models/',

    /**
     * Initialize the Face Recognition System with a singleton promise
     */
    async init(progressCallback) {
        if (this.modelsLoaded) {
            if (progressCallback) progressCallback('complete');
            return true;
        }

        if (this._initPromise) {
            return this._initPromise;
        }

        this._initPromise = (async () => {
            console.log("%c AI: Initializing Face System...", "color: #6c5ce7; font-weight: bold;");
            this.status = 'loading';
            this.updateGlobalStatus();

            try {
                // Initialize DB first
                await this.initDB();

                // Load Models with a timeout
                const loadTask = this.loadModels(progressCallback);
                const timeoutTask = new Promise((_, reject) =>
                    setTimeout(() => reject(new Error("Model loading timed out (20s)")), 20000)
                );

                await Promise.race([loadTask, timeoutTask]);

                this.modelsLoaded = true;
                this.status = 'ready';
                console.log("%c AI: System Ready.", "color: #2ecc71; font-weight: bold;");
                if (progressCallback) progressCallback('complete');
                return true;
            } catch (error) {
                this.status = 'error';
                this._initPromise = null; // Allow retry
                console.error("AI: Initialization failed:", error);
                if (progressCallback) progressCallback('error', error.message);
                this.updateGlobalStatus();
                return false;
            } finally {
                this.updateGlobalStatus();
            }
        })();

        return this._initPromise;
    },

    /**
     * Load models with sequential progress tracking
     */
    async loadModels(callback) {
        const models = [
            { name: 'Face Detection', load: () => faceapi.nets.ssdMobilenetv1.loadFromUri(this.modelUrl) },
            { name: 'Face Landmarks', load: () => faceapi.nets.faceLandmark68Net.loadFromUri(this.modelUrl) },
            { name: 'Face Recognition', load: () => faceapi.nets.faceRecognitionNet.loadFromUri(this.modelUrl) }
        ];

        for (let i = 0; i < models.length; i++) {
            const model = models[i];
            if (callback) callback('progress', {
                current: i + 1,
                total: models.length,
                name: model.name,
                percent: Math.round(((i) / models.length) * 100)
            });

            await model.load();
        }
    },

    updateGlobalStatus() {
        const indicator = document.getElementById('face-ai-status');
        if (!indicator) return;

        const colors = { loading: '#f1c40f', ready: '#2ecc71', error: '#e74c3c' };
        const labels = { loading: 'AI: Loading...', ready: 'AI: Ready', error: 'AI: Error' };

        indicator.style.backgroundColor = colors[this.status];
        indicator.parentElement.title = labels[this.status];

        if (this.status === 'loading') indicator.classList.add('ai-pulse');
        else indicator.classList.remove('ai-pulse');
    },

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, 1);
            request.onerror = () => reject("IndexedDB failure");
            request.onsuccess = (e) => { this.db = e.target.result; resolve(); };
            request.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    db.createObjectStore(this.storeName, { keyPath: "studentId" });
                }
            };
        });
    },

    async startVideo(videoElement) {
        try {
            const constraints = {
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: "user"
                }
            };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoElement.srcObject = stream;

            // Wait for video to be ready
            return new Promise((resolve) => {
                videoElement.onloadedmetadata = () => {
                    videoElement.play();
                    resolve(true);
                };
            });
        } catch (err) {
            console.error("Webcam Access Error:", err);
            return false;
        }
    },

    /**
     * Calculate average brightness of a video/canvas element
     */
    getBrightness(element) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 100; // Small sample
        canvas.height = 100;
        ctx.drawImage(element, 0, 0, 100, 100);
        const data = ctx.getImageData(0, 0, 100, 100).data;
        let r, g, b, avg = 0;
        for (let i = 0; i < data.length; i += 4) {
            r = data[i]; g = data[i+1]; b = data[i+2];
            avg += (r * 0.299 + g * 0.587 + b * 0.114);
        }
        return avg / (data.length / 4);
    },

    stopVideo(videoElement) {
        if (videoElement.srcObject) {
            videoElement.srcObject.getTracks().forEach(track => track.stop());
            videoElement.srcObject = null;
        }
    },

    async getFaceDescriptor(element) {
        const detection = await faceapi.detectSingleFace(element, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks().withFaceDescriptor();
        return detection;
    },

    async enrollStudent(studentId, fullName, descriptor) {
        const transaction = this.db.transaction([this.storeName], "readwrite");
        return transaction.objectStore(this.storeName).put({
            studentId, fullName, descriptor: Array.from(descriptor),
            updatedAt: new Date().toISOString()
        });
    },

    async getAllEnrolled() {
        return new Promise((resolve) => {
            if (!this.db) return resolve([]);
            const transaction = this.db.transaction([this.storeName], "readonly");
            const request = transaction.objectStore(this.storeName).getAll();
            request.onsuccess = () => resolve(request.result.map(item => ({
                ...item, descriptor: new Float32Array(item.descriptor)
            })));
            request.onerror = () => resolve([]);
        });
    },

    async deleteEnrollment(studentId) {
        if (!this.db) return;
        const transaction = this.db.transaction([this.storeName], "readwrite");
        transaction.objectStore(this.storeName).delete(studentId);
    },

    async recognizeFace(videoElement, enrolledData) {
        if (!enrolledData?.length) return null;
        const labeled = enrolledData.map(item => new faceapi.LabeledFaceDescriptors(item.studentId, [item.descriptor]));
        const matcher = new faceapi.FaceMatcher(labeled, 0.6);
        const detection = await faceapi.detectSingleFace(videoElement, new faceapi.SsdMobilenetv1Options())
            .withFaceLandmarks().withFaceDescriptor();

        if (!detection) return null;
        const bestMatch = matcher.findBestMatch(detection.descriptor);
        return {
            studentId: bestMatch.label,
            distance: bestMatch.distance,
            fullName: enrolledData.find(d => d.studentId === bestMatch.label)?.fullName || 'Unknown',
            detection
        };
    }
};
