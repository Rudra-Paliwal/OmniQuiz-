// ===== FILE: c_tools/verifier.c =====
#include <stdio.h>
#include <string.h>

int main() {
    FILE *f = fopen("responses_log.txt", "r");
    if (!f) {
        printf("File not found.\n");
        return 1;
    }

    char line[256];
    int prev_hash = 0;
    while (fgets(line, sizeof(line), f)) {
        int current_hash = 0;
        for (int i = 0; i < strlen(line); i++) {
            current_hash += line[i];
        }

        if (prev_hash != 0 && current_hash <= prev_hash) {
            printf("Tampering detected!\n");
            fclose(f);
            return 1;
        }
        prev_hash = current_hash;
    }

    fclose(f);
    printf("Integrity verified.\n");
    return 0;
}
