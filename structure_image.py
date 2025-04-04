# -*- coding: utf-8 -*-
import sys
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches

input_file = sys.argv[1]
output_image = sys.argv[2]

sequence = []
helix = []
sheet = []
turns = []
coil = []

legend_elements = [
    mpatches.Patch(color='lightgray', label='coil'),
    mpatches.Patch(color='red', label='helix'),
    mpatches.Patch(color='blue', label='sheet'),
    mpatches.Patch(color='green', label='turn')
]

with open(input_file, 'r') as f:
    lines = f.readlines()
    i = 0
    while i < len(lines):
        if lines[i].startswith('helix'):
            seq_line = lines[i - 1].strip()
            helix_line = lines[i].strip().split('helix')[1].strip()
            sheet_line = lines[i + 1].strip().split('sheet')[1].strip()
            turns_line = lines[i + 2].strip().split('turns')[1].strip()
            coil_line = lines[i + 3].strip().split('coil')[1].strip()

            sequence.append(seq_line)
            helix.append(helix_line)
            sheet.append(sheet_line)
            turns.append(turns_line)
            coil.append(coil_line)
            i += 4
        else:
            i += 1

# 合并多段为完整的结构
sequence = ''.join(sequence)
helix = ''.join(helix)
sheet = ''.join(sheet)
turns = ''.join(turns)
coil = ''.join(coil)

# 可视化部分
plt.figure(figsize=(12, 2))
x = range(1, len(sequence) + 1)

# 绘制背景 coil
plt.bar(x, [1] * len(sequence), color='lightgray', edgecolor='none')

# 叠加其它结构
for i in range(len(sequence)):
    if i < len(helix) and helix[i] == 'H':
        plt.bar(x[i], 1, color='red', edgecolor='none')
    elif i < len(sheet) and sheet[i] == 'E':
        plt.bar(x[i], 1, color='blue', edgecolor='none')
    elif i < len(turns) and turns[i] == 'T':
        plt.bar(x[i], 1, color='green', edgecolor='none')

# 手动插入图注
plt.legend(handles=legend_elements, loc='upper right')


plt.title('Secondary Structure Prediction')
plt.xlabel('Residue Position')
plt.yticks([])
plt.tight_layout()
plt.savefig(output_image, dpi=300)
plt.close()

