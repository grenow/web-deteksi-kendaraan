from flask import Flask, render_template, request, Response, jsonify
from ultralytics import YOLO
import cv2
import os

app = Flask(__name__)

# Inisialisasi model YOLO
model = YOLO('best1.pt')  # Ganti dengan model Anda

# Direktori untuk penyimpanan file sementara
UPLOAD_FOLDER = 'uploads'
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/videos', methods=['GET'])
def list_videos():
    """Mengembalikan daftar video yang diunggah."""
    files = os.listdir(UPLOAD_FOLDER)
    return jsonify({'videos': files})

@app.route('/upload', methods=['POST'])
def upload_video():
    """Endpoint untuk mengunggah video ke server."""
    if 'video' not in request.files:
        return "No file uploaded", 400
    
    file = request.files['video']
    if file.filename == '':
        return "No file selected", 400

    # Simpan file ke folder upload
    filepath = os.path.join(UPLOAD_FOLDER, file.filename)
    file.save(filepath)
    return "File uploaded successfully", 200

def generate_video_stream(video_path):
    """Mengambil frame dari video yang diunggah dan mengirimkannya secara real-time dengan bounding box."""
    cap = cv2.VideoCapture(video_path)
    if not cap.isOpened():
        print(f"Failed to open video: {video_path}")

    while cap.isOpened():
        ret, frame = cap.read()
        if not ret:
            print("No more frames to read.")
            break

        # Resize frame
        frame = cv2.resize(frame, (640, 360))

        # Jalankan deteksi YOLO pada frame
        results = model.predict(source=frame, conf=0.6, save=False)

        # Annotate frame
        annotated_frame = results[0].plot()
        annotated_frame = cv2.resize(annotated_frame, (640, 360))

        # Encode frame menjadi format JPEG
        _, buffer = cv2.imencode('.jpg', annotated_frame)
        frame = buffer.tobytes()

        # Kirim frame sebagai stream MJPEG
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

    cap.release()

@app.route('/video/<filename>')
def stream_video(filename):
    """Endpoint untuk streaming video dengan bounding box."""
    video_path = os.path.join(UPLOAD_FOLDER, filename)
    if not os.path.exists(video_path):
        return "Video not found", 404
    return Response(generate_video_stream(video_path),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
