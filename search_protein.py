# -*- coding: utf-8 -*-
#This Python script receives the protein family name and species name as parameters, queries up to 30 matching protein sequence information through NCBI Entrez, extracts key fields (such as accession, name, species, sequence, etc.), and outputs the results in JSON format to the result web page for parsing.

import sys
import json
from Bio import Entrez
Entrez.email = "1969316573@qq.com"  
Entrez.api_key = "68efb9fba155256968c40b213bc146891908"  

def search_protein_sequences(protein_family, taxonomic_group):
    queries = [
        f'{protein_family}[Protein Name] AND {taxonomic_group}[Organism]',
        f'"{protein_family}"[All Fields] AND "{taxonomic_group}"[All Fields]'
        f'{protein_family} {taxonomic_group}'
    ]

    for i, query in enumerate(queries):
        handle = Entrez.esearch(db="protein", term=query, retmax=1000)
        record = Entrez.read(handle)
        handle.close()

        if record["Count"] != "0":
            id_list = record["IdList"]
            handle = Entrez.efetch(db="protein", id=id_list, rettype="gb", retmode="xml")
            records = Entrez.read(handle)
            handle.close()

            result_data = []
            for record in records:
                accession = str(record.get("GBSeq_primary-accession", ""))
                definition = record.get("GBSeq_definition", "")
                protein_name = definition.split(' [')[0]
                protein_data = {
                    "accession": accession,
                    "protein_name": protein_name,
                    "organism": record.get("GBSeq_organism", ""),
                    "sequence_length": record.get("GBSeq_length", ""),
                    "sequence": record.get("GBSeq_sequence", ""),
                }
                result_data.append(protein_data)
            return json.dumps(result_data)

    return json.dumps({"error": "No sequences found"})


#It is used to receive two parameters from the command line: protein family name (protein_family) and taxonomic group name (taxonomic_group), then call the search_protein_sequences() function to query and print the results to standard output. If the number of input parameters is incorrect, an error message (JSON format) is output and the program exits.
if __name__ == "__main__":
    if len(sys.argv) != 3:
        print(json.dumps({"error": "Invalid input parameters"}))
        sys.exit(1)
    
    protein_family = sys.argv[1]
    taxonomic_group = sys.argv[2]
    result = search_protein_sequences(protein_family, taxonomic_group)
    

    print(result)
    sys.stdout.flush()
