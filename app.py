from flask import Flask, render_template, request, redirect, url_for, flash, send_file, session
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from flask_bcrypt import Bcrypt
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from io import BytesIO
import csv
import os
from datetime import datetime

app = Flask(__name__)
app.config['SECRET_KEY'] = 'change_this_to_a_strong_random_secret_key_2025'
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///school.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['UPLOAD_FOLDER'] = 'uploads'

db = SQLAlchemy(app)
bcrypt = Bcrypt(app)
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# Models
class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(150), unique=True, nullable=False)
    password = db.Column(db.String(150), nullable=False)
    role = db.Column(db.String(50), nullable=False)  # 'teacher', 'pupil', 'admin'
    email = db.Column(db.String(150))
    phone = db.Column(db.String(50))

class Pupil(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(150), nullable=False)
    class_name = db.Column(db.String(50))
    pupil_id = db.Column(db.String(50), unique=True, nullable=False)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'))

class Subject(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    class_name = db.Column(db.String(50))

class Result(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    pupil_id = db.Column(db.Integer, db.ForeignKey('pupil.id'))
    subject_id = db.Column(db.Integer, db.ForeignKey('subject.id'))
    continuous_assessment = db.Column(db.Float)
    exam_score = db.Column(db.Float)
    total = db.Column(db.Float)
    comment = db.Column(db.Text)
    term = db.Column(db.String(50))
    year = db.Column(db.Integer)
    locked = db.Column(db.Boolean, default=False)

class Announcement(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    message = db.Column(db.Text, nullable=False)
    date = db.Column(db.DateTime, default=datetime.utcnow)

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

@app.route('/')
def index():
    announcements = Announcement.query.order_by(Announcement.date.desc()).limit(5).all()
    return render_template('index.html', announcements=announcements)

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        user = User.query.filter_by(username=username).first()
        if user and bcrypt.check_password_hash(user.password, password):
            login_user(user)
            return redirect(url_for('dashboard'))
        flash('Invalid username or password', 'danger')
    return render_template('login.html')

@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('index'))

@app.route('/register', methods=['GET', 'POST'])
@login_required
def register():
    if current_user.role != 'admin':
        flash('Access denied')
        return redirect(url_for('dashboard'))
    if request.method == 'POST':
        username = request.form['username']
        password = bcrypt.generate_password_hash(request.form['password']).decode('utf-8')
        role = request.form['role']
        email = request.form.get('email')
        phone = request.form.get('phone')
        new_user = User(username=username, password=password, role=role, email=email, phone=phone)
        db.session.add(new_user)
        db.session.commit()
        flash('User registered successfully')
    return render_template('register.html')

@app.route('/dashboard')
@login_required
def dashboard():
    if current_user.role == 'teacher':
        return render_template('teacher_dashboard.html')
    elif current_user.role == 'pupil':
        pupil = Pupil.query.filter_by(user_id=current_user.id).first()
        if not pupil:
            flash('Pupil profile not linked')
        results = Result.query.filter_by(pupil_id=pupil.id).all() if pupil else []
        return render_template('pupil_dashboard.html', pupil=pupil, results=results)
    elif current_user.role == 'admin':
        users = User.query.all()
        return render_template('admin_dashboard.html', users=users)
    return redirect(url_for('index'))

@app.route('/enter_results', methods=['GET', 'POST'])
@login_required
def enter_results():
    if current_user.role != 'teacher':
        return redirect(url_for('dashboard'))
    pupils = Pupil.query.all()
    subjects = Subject.query.all()
    if request.method == 'POST':
        pupil_id = request.form['pupil_id']
        subject_id = request.form['subject_id']
        ca = float(request.form.get('ca', 0))
        exam = float(request.form.get('exam', 0))
        total = ca + exam
        comment = request.form['comment']
        term = request.form['term']
        year = request.form['year']
        new_result = Result(pupil_id=pupil_id, subject_id=subject_id, continuous_assessment=ca, exam_score=exam, total=total, comment=comment, term=term, year=year)
        db.session.add(new_result)
        db.session.commit()
        flash('Results entered successfully')
    return render_template('enter_results.html', pupils=pupils, subjects=subjects)

@app.route('/generate_report/<int:pupil_id>')
@login_required
def generate_report(pupil_id):
    pupil = Pupil.query.get_or_404(pupil_id)
    results = Result.query.filter_by(pupil_id=pupil_id).all()
    buffer = BytesIO()
    p = canvas.Canvas(buffer, pagesize=letter)
    p.drawString(100, 750, f"Official Report Card")
    p.drawString(100, 730, f"Pupil: {pupil.name} (ID: {pupil.pupil_id})")
    p.drawString(100, 710, f"Class: {pupil.class_name}")
    p.drawString(100, 690, "Mangana Primary/Secondary School")
    y = 650
    for result in results:
        subject = Subject.query.get(result.subject_id)
        subj_name = subject.name if subject else 'Unknown'
        p.drawString(100, y, f"{subj_name}: CA {result.continuous_assessment} | Exam {result.exam_score} | Total {result.total} | Comment: {result.comment or 'N/A'}")
        p.drawString(400, y, f"Term: {result.term} {result.year}")
        y -= 30
    p.save()
    buffer.seek(0)
    return send_file(buffer, as_attachment=True, download_name=f"{pupil.name.replace(' ', '_')}_report.pdf", mimetype='application/pdf')

@app.route('/view_results')
@login_required
def view_results():
    if current_user.role != 'pupil':
        return redirect(url_for('dashboard'))
    pupil = Pupil.query.filter_by(user_id=current_user.id).first()
    if not pupil:
        flash('No pupil profile')
        return redirect(url_for('dashboard'))
    results = Result.query.filter_by(pupil_id=pupil.id).all()
    return render_template('view_results.html', pupil=pupil, results=results)

@app.route('/announcements')
def announcements():
    anns = Announcement.query.order_by(Announcement.date.desc()).all()
    return render_template('announcements.html', announcements=anns)

@app.route('/search', methods=['GET'])
@login_required
def search():
    if current_user.role not in ['teacher', 'admin']:
        return redirect(url_for('dashboard'))
    query = request.args.get('q', '')
    pupils = Pupil.query.filter(Pupil.name.ilike(f'%{query}%') | Pupil.pupil_id.ilike(f'%{query}%') | Pupil.class_name.ilike(f'%{query}%')).all()
    return render_template('search.html', pupils=pupils, query=query)

@app.route('/add_announcement', methods=['POST'])
@login_required
def add_announcement():
    if current_user.role == 'admin':
        message = request.form['message']
        new_ann = Announcement(message=message)
        db.session.add(new_ann)
        db.session.commit()
        flash('Announcement added')
    return redirect(url_for('announcements'))

# Placeholder for upload class list
@app.route('/upload_class_list', methods=['GET', 'POST'])
@login_required
def upload_class_list():
    if current_user.role != 'teacher':
        return redirect(url_for('dashboard'))
    if request.method == 'POST':
        # Simple placeholder - in real add file handling
        flash('Upload feature placeholder - add CSV parsing here')
    return render_template('upload_class.html')

if __name__ == '__main__':
    os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)
    with app.app_context():
        db.create_all()
        # Create default admin if not exists
        if not User.query.filter_by(role='admin').first():
            admin_pass = bcrypt.generate_password_hash('admin123').decode('utf-8')
            admin = User(username='admin', password=admin_pass, role='admin', email='admin@school.com')
            db.session.add(admin)
            db.session.commit()
    app.run(debug=True)
