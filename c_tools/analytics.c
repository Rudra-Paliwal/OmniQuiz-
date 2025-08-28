// ===== FILE: c_tools/analytics.c =====
#include <stdio.h>

int main() {
    FILE *f = fopen("responses_data.csv", "r");
    if (!f) {
        printf("Data file not found.\n");
        return 1;
    }

    int score, total = 0, count = 0;
    while (fscanf(f, "%*d,%*d,%*c,%*d,%*d,%*d,%d\n", &score) != EOF) {
        total += score;
        count++;
    }

    fclose(f);
    printf("Average Score: %.2f\n", count ? (float)total / count : 0);
    return 0;
}
