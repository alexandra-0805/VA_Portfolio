from pathlib import Path
text = '''ALEXANDRA LYKEMAE T. CONCEPCION
VIRTUAL ASSISTANT | ADMIN SUPPORT | SOCIAL MEDIA & DIGITAL ASSISTANT
Paliparan 3, City of Dasmarinas, Cavite, Philippines | lykemaeconcepcion@gmail.com | +63-994-098-0220

PROFESSIONAL SUMMARY
Detail-oriented and adaptable aspiring Virtual Assistant with strong communication, organization,
and problem-solving skills. Experienced in managing customer inquiries, handling business
operations, creating digital content, and working with online tools. Skilled in administrative tasks,
research, documentation, and client communication. A fast learner with the ability to manage
multiple tasks while providing reliable support to businesses and clients.

EDUCATION
KOLEHIYO NG LUNGSOD NG DASMARINAS | Cavite, PH
Bachelor of Science in Information System | 2024 - 2026
PALIPARAN III SENIOR HIGH SCHOOL | Cavite, PH
Accountancy and Business Management | 2023 - 2024

EXPERIENCES
BAKESHOP WEBSITE PROJECT - Front-End Developer
- Collaborated with clients to gather requirements and implement solutions.
- Demonstrated attention to detail, teamwork, and effective communication.
- Developed a user-friendly website to improve customer engagement.
WOODEN PERFUME BOTTLE - Product Developer
- Worked with a team to develop and present an innovative product concept.
- Applied organizational, presentation, and customer-focused thinking skills.
SOLE PROPRIETORSHIP - Small Business Owner
- Managed daily business operations, customer inquiries, and sales transactions.
- Provided quality customer service and resolved customer concerns effectively.
- Developed strong communication, multitasking, and problem-solving abilities.

CERTIFICATES
MIBEX
Entrepreneurship - Jan. 2024
Establishing & Operating Micro-Small Medium Enterprises - April 2024
Most Sustainable Product in Mini Customized Perfume Bottle - April 2024
Best Non-food in Product - April 2024
Visual Graphic Design NC 2 - Feb. 2026

SKILLS & TOOLS
Administrative Support | Email & Client Communication | Data Entry | Internet Research
File Management | Customer Support | Canva Design | Microsoft Office | Google Workspace
AI Tools | Basic Web Development'''

def esc(value):
    return value.replace('\\', '\\\\').replace('(', '\\(').replace(')', '\\)')

commands = ['BT', '54 748 Td', '/F1 9 Tf']
for line in text.splitlines():
    commands += [f'({esc(line)}) Tj', '0 -14 Td']
stream = ('\n'.join(commands) + '\nET\n').encode('ascii')
objects = [b'<< /Type /Catalog /Pages 2 0 R >>', b'<< /Type /Pages /Kids [3 0 R] /Count 1 >>', b'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>', b'<< /Length ' + str(len(stream)).encode() + b' >>\nstream\n' + stream + b'endstream', b'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>']
pdf = b'%PDF-1.4\n'
offsets = [0]
for number, obj in enumerate(objects, 1):
    offsets.append(len(pdf)); pdf += f'{number} 0 obj\n'.encode() + obj + b'\nendobj\n'
xref = len(pdf); pdf += f'xref\n0 {len(objects)+1}\n0000000000 65535 f \n'.encode(); pdf += b''.join(f'{offset:010d} 00000 n \n'.encode() for offset in offsets[1:]); pdf += f'trailer\n<< /Size {len(objects)+1} /Root 1 0 R >>\nstartxref\n{xref}\n%%EOF\n'.encode()
Path('assets/Alexandra_Concepcion_VA.pdf').write_bytes(pdf)
