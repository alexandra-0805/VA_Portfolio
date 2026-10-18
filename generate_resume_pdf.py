from pathlib import Path

lines = [
    (18, 'ALEXANDRA LYKEMAE T. CONCEPCION'),
    (10, 'VIRTUAL ASSISTANT | ADMIN SUPPORT | SOCIAL MEDIA & DIGITAL ASSISTANT'),
    (9, 'Paliparan 3, City of Dasmarinas, Cavite, Philippines | lykemaeconcepcion@gmail.com | +63-994-098-0220'),
    (13, 'Professional Summary'),
    (9, 'Detail-oriented and adaptable aspiring Virtual Assistant with strong communication,'),
    (9, 'organization, and problem-solving skills. Experienced in managing customer inquiries,'),
    (9, 'handling business operations, creating digital content, and working with online tools.'),
    (9, 'Skilled in administrative tasks, research, documentation, and client communication.'),
    (9, 'A fast learner with the ability to manage multiple tasks while providing reliable support.'),
    (13, 'Education'),
    (9, 'KOLEHIYO NG LUNGSOD NG DASMARINAS | Cavite, PH'),
    (9, 'Bachelor of Science in Information System | 2024 - 2026'),
    (9, 'PALIPARAN III SENIOR HIGH SCHOOL | Cavite, PH'),
    (9, 'Accountancy and Business Management | 2023 - 2024'),
    (13, 'Experiences'),
    (9, 'BAKESHOP WEBSITE PROJECT - Front-End Developer'),
    (9, '- Collaborated with clients to gather requirements and implement solutions.'),
    (9, '- Demonstrated attention to detail, teamwork, and effective communication.'),
    (9, '- Developed a user-friendly website to improve customer engagement.'),
    (9, 'WOODEN PERFUME BOTTLE - Product Developer'),
    (9, '- Worked with a team to develop and present an innovative product concept.'),
    (9, '- Applied organizational, presentation, and customer-focused thinking skills.'),
    (9, 'SOLE PROPRIETORSHIP - Small Business Owner'),
    (9, '- Managed daily business operations, customer inquiries, and sales transactions.'),
    (9, '- Provided quality customer service and resolved customer concerns effectively.'),
    (9, '- Developed strong communication, multitasking, and problem-solving abilities.'),
    (13, 'Certificates'),
    (9, 'MIBEX'),
    (9, 'Entrepreneurship - Jan. 2024'),
    (9, 'Establishing & Operating Micro-Small Medium Enterprises - April 2024'),
    (9, 'Most Sustainable Product in Mini Customized Perfume Bottle - April 2024'),
    (9, 'Best Non-food in Product - April 2024'),
    (9, 'Visual Graphic Design NC 2 - Feb. 2026'),
    (13, 'Skills & Tools'),
    (9, 'Administrative Support | Email & Client Communication | Data Entry | Internet Research'),
    (9, 'File Management | Customer Support | Canva Design | Microsoft Office | Google Workspace'),
    (9, 'AI Tools | Basic Web Development'),
]

def escape(text):
    return text.replace('\\', '\\\\').replace('(', '\\(').replace(')', '\\)')

commands = ['BT', '54 748 Td']
for size, text in lines:
    commands.extend([f'/{size} Tf', f'({escape(text)}) Tj', '0 -18 Td'])
stream = '\n'.join(commands) + '\nET\n'
stream_bytes = stream.encode('ascii')
objects = [
    b'<< /Type /Catalog /Pages 2 0 R >>',
    b'<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
    b'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
    b'<< /Length ' + str(len(stream_bytes)).encode('ascii') + b' >>\nstream\n' + stream_bytes + b'endstream',
    b'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
]

pdf = b'%PDF-1.4\n'
offsets = [0]
for number, obj in enumerate(objects, 1):
    offsets.append(len(pdf))
    pdf += f'{number} 0 obj\n'.encode('ascii') + obj + b'\nendobj\n'
xref = len(pdf)
pdf += f'xref\n0 {len(objects) + 1}\n0000000000 65535 f \n'.encode('ascii')
pdf += b''.join(f'{offset:010d} 00000 n \n'.encode('ascii') for offset in offsets[1:])
pdf += f'trailer\n<< /Size {len(objects) + 1} /Root 1 0 R >>\nstartxref\n{xref}\n%%EOF\n'.encode('ascii')
Path('assets/Alexandra_Concepcion_VA.pdf').write_bytes(pdf)
