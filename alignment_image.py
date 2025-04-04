# -*- coding: utf-8 -*-
# The workflow of the PHP part is to obtain the accession (sequence ID) selected by the user from the form, query the actual content of these sequences (amino acid sequences) from the database, write the query results into a temporary FASTA file, call Clustal Omega for multiple sequence alignment, output .aln file, call Python script (alignment_image.py) to generate three images (text alignment map, heat map, conservation profile), record these image paths and alignment text into the database analysis_history table (if the user is logged in), and display the alignment text and three images on the page. Since the code part is long, I wrote the main functions under each function.
import sys
import subprocess
import matplotlib.pyplot as plt
import numpy as np
from Bio import AlignIO
import os
import warnings
import re
import matplotlib
os.environ['MPLCONFIGDIR'] = '/tmp'
warnings.filterwarnings("ignore", category=UserWarning, module="matplotlib")

def generate_alignment_images(input_file, output_base):
    """
    Generate three figures:
    1. Text-based alignment image: {output_base}_text.png
    2. Conservation heatmap: {output_base}_heatmap.png
    3. Conservation profile (line chart): {output_base}_profile.png
    """
    
    alignment_file = input_file.replace('.fasta', '_aligned.aln')
    subprocess.run(["clustalo", "-i", input_file, "-o", alignment_file, "--force", "--outfmt=clu"])

    with open(alignment_file, 'r', encoding='utf-8', errors='replace') as f:
        alignment_text = f.read()
    plt.figure(figsize=(12, 8))
    plt.text(0.01, 0.99, alignment_text,
             fontsize=10, fontfamily='monospace',
             transform=plt.gca().transAxes, verticalalignment='top')
    plt.axis('off')
    plt.tight_layout()
    text_file = output_base + "_text.png"
    plt.savefig(text_file, bbox_inches='tight')
    plt.close()

    alignment = AlignIO.read(alignment_file, "clustal")
    sequences = [str(record.seq) for record in alignment]
    seq_array = np.array([list(seq) for seq in sequences])  

    def shannon_entropy(column):
        vals, counts = np.unique(column, return_counts=True)
        freqs = counts / counts.sum()
        return -np.sum(freqs * np.log2(freqs))

    max_entropy = np.log2(20)  
    entropy_scores = np.array([shannon_entropy(seq_array[:, i]) for i in range(seq_array.shape[1])])
    conservation_scores = max_entropy - entropy_scores 

  
    heatmap_data = np.tile(conservation_scores, (10, 1))
    plt.figure(figsize=(12, 2))
    plt.imshow(heatmap_data, aspect='auto', cmap='viridis')
    plt.colorbar(label='Conservation Score')
    plt.xlabel('Position')
    plt.title('Conservation Heatmap')
    heatmap_file = output_base + "_heatmap.png"
    plt.savefig(heatmap_file, bbox_inches='tight')
    plt.close()

    
    plt.figure(figsize=(12, 4))
    plt.plot(conservation_scores, marker='o', linestyle='-')
    plt.xlabel('Position')
    plt.ylabel('Conservation Score')
    plt.title('Conservation Profile')
    profile_file = output_base + "_profile.png"
    plt.savefig(profile_file, bbox_inches='tight')
    plt.close()

    return [text_file, heatmap_file, profile_file]


if __name__ == "__main__":
    """
    Usage:
    1) python3 alignment_image.py <input_fasta> <output_base>
       - Original two-argument usage
    2) python3 alignment_image.py <input_fasta> <output_base> <acc_str>
       - If a third arg is provided, it will be appended to output_base
         so final files = output_base_<acc_str>_text.png, etc.
    """
    if len(sys.argv) < 3:
        print("Usage: python3 alignment_image.py <input_fasta> <output_base> [<acc_str>]")
        sys.exit(1)

    input_file = sys.argv[1]
    output_base = sys.argv[2]

    if len(sys.argv) >= 4:
        acc_str = sys.argv[3]
        acc_str = re.sub(r'[^A-Za-z0-9_]', '_', acc_str)
        output_base += "_" + acc_str

    files = generate_alignment_images(input_file, output_base)