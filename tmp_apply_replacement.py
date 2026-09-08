from pathlib import Path
import re

controller_path = Path('app/Http/Controllers/LibraryOperationsController.php')
replacement_path = Path('tmp_admin_replacement.py')

text = controller_path.read_text(encoding='utf-8')
replacement = replacement_path.read_text(encoding='utf-8')

start_marker = 'public function adminDashboard()'
end_marker = 'public function studentDashboard()'

start = text.find(start_marker)
if start == -1:
    raise SystemExit('start marker not found')
end = text.find(end_marker, start)
if end == -1:
    raise SystemExit('end marker not found')

# find end of method by matching braces from first '{' after start
brace_start = text.find('{', start)
if brace_start == -1:
    raise SystemExit('opening brace not found')

level = 0
method_end = None
for i in range(brace_start, len(text)):
    if text[i] == '{':
        level += 1
    elif text[i] == '}':
        level -= 1
        if level == 0:
            method_end = i + 1
            break

if method_end is None:
    raise SystemExit('could not determine end of method')

new_text = text[:start] + replacement + text[method_end:]
controller_path.write_text(new_text, encoding='utf-8')
print('replaced adminDashboard block')
