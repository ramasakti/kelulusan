import React from "react";
import { Head, useForm } from "@inertiajs/react";
import {
  Search,
  GraduationCap,
  Award,
  AlertCircle,
  BookOpen,
  User,
  Hash,
  TrendingUp,
  CheckCircle2,
  XCircle,
  School,
  ArrowRight,
  RefreshCw
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert";
import {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell
} from "@/components/ui/table";
import { Separator } from "@/components/ui/separator";

// TS Interfaces
export interface NilaiItem {
  mata_pelajaran: string;
  nilai: number | null;
}

export interface SiswaData {
  nama_siswa: string;
  nisn: string;
  status_kelulusan: "LULUS" | "TIDAK LULUS";
  rata_rata: number;
  nilai: NilaiItem[];
}

export interface KelulusanProps {
  search: string | null;
  siswa: SiswaData | null;
  error: string | null;
}

export default function Kelulusan({ search, siswa, error }: KelulusanProps) {
  const { data, setData, get, processing } = useForm({
    nisn: search || "",
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!data.nisn.trim()) return;

    get("/kelulusan", {
      preserveState: true,
      preserveScroll: true,
    });
  };

  return (
    <>
      <Head>
        <title>Pengumuman Kelulusan Siswa - Portal Resmi Sekolah</title>
        <meta name="description" content="Halaman resmi pengecekan status kelulusan siswa tahun ajaran 2025/2026." />
      </Head>

      <div className="min-h-screen bg-brand-soft-bg/30 px-4 py-8 md:py-16 flex flex-col items-center justify-center font-sans">
        <div className="w-full max-w-[800px] space-y-6">

          {/* Main Container Card */}
          <Card className="overflow-hidden border-none shadow-xl bg-white rounded-2xl">

            {/* Hero Section with custom brand gradient */}
            <div className="relative p-8 md:p-12 text-white overflow-hidden bg-linear-to-br from-brand-primary via-brand-secondary to-brand-accent">
              {/* Background abstract decoration shapes for premium look */}
              <div className="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none" />
              <div className="absolute bottom-0 left-0 w-48 h-48 bg-black/5 rounded-full blur-2xl -ml-16 -mb-16 pointer-events-none" />

              <div className="relative flex flex-col items-center text-center space-y-6">

                {/* School Logo Shield SVG */}
                <div className="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner">
                  <svg className="w-12 h-12 text-white drop-shadow-md" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 4L4 16L32 28L56 18V38H60V16L32 4Z" fill="currentColor" />
                    <path d="M12 23V39C12 45.6 21 51 32 51C43 51 52 45.6 52 39V23L32 32.5L12 23Z" fill="currentColor" opacity="0.85" />
                    <path d="M32 36L20 31V37.5C20 41 25.4 44 32 44C38.6 44 44 41 44 37.5V31L32 36Z" fill="currentColor" />
                  </svg>
                </div>

                {/* School Information */}
                <div className="space-y-2">
                  <span className="text-xs font-semibold tracking-widest uppercase bg-white/20 px-3 py-1 rounded-full text-white/90 backdrop-blur-xs">
                    Portal Resmi Sekolah
                  </span>
                  <h2 className="text-lg md:text-xl font-medium tracking-wide text-brand-soft-bg">
                    SMP ISLAM PARLAUNGAN
                  </h2>
                  <h1 className="text-3xl md:text-4xl font-extrabold tracking-tight drop-shadow-xs">
                    Pengumuman Kelulusan
                  </h1>
                  <p className="text-xs md:text-sm max-w-md mx-auto text-white/80 leading-relaxed font-light">
                    Masukkan Nomor Induk Siswa Nasional (NISN) Anda pada kolom di bawah untuk memeriksa status kelulusan tahun ajaran 2025/2026.
                  </p>
                </div>

                {/* Search Form */}
                <form onSubmit={handleSubmit} className="w-full max-w-md space-y-3 pt-2">
                  <div className="relative flex items-center">
                    <div className="absolute left-3.5 text-muted-foreground pointer-events-none flex items-center">
                      <Hash className="w-4 h-4 text-brand-primary" />
                    </div>
                    <Input
                      type="text"
                      placeholder="Masukkan 10 digit NISN..."
                      value={data.nisn}
                      onChange={(e) => setData("nisn", e.target.value)}
                      disabled={processing}
                      maxLength={15}
                      className="pl-10 pr-4 h-12 bg-white text-slate-800 border-none shadow-lg rounded-xl text-base placeholder:text-slate-400 focus-visible:ring-2 focus-visible:ring-brand-accent focus-visible:ring-offset-2 transition-all w-full"
                    />
                  </div>

                  <Button
                    type="submit"
                    disabled={processing || !data.nisn.trim()}
                    className="w-full h-12 text-base font-semibold shadow-lg hover:shadow-xl transition-all rounded-xl cursor-pointer disabled:cursor-not-allowed bg-brand-primary hover:bg-brand-primary/95 text-white flex items-center justify-center gap-2 border border-white/10"
                  >
                    {processing ? (
                      <>
                        <RefreshCw className="w-5 h-5 animate-spin" />
                        Memproses Data...
                      </>
                    ) : (
                      <>
                        <Search className="w-5 h-5" />
                        Cek Kelulusan Siswa
                      </>
                    )}
                  </Button>
                </form>

              </div>
            </div>

            {/* Results Section */}
            <CardContent className="p-6 md:p-8">

              {/* Not Found / Error State */}
              {error && (
                <div className="animate-in fade-in slide-in-from-bottom-4 duration-300">
                  <Alert variant="destructive" className="bg-rose-50/50 border-rose-200 dark:bg-rose-950/20 dark:border-rose-900 rounded-xl p-5">
                    <AlertCircle className="h-5 w-5 text-rose-600 dark:text-rose-400" />
                    <AlertTitle className="text-base font-bold text-rose-800 dark:text-rose-300 ml-2">
                      Pencarian Gagal
                    </AlertTitle>
                    <AlertDescription className="text-sm text-rose-700 dark:text-rose-400 mt-1 ml-2">
                      {error}
                    </AlertDescription>
                  </Alert>
                </div>
              )}

              {/* Initial State (No search query and no results yet) */}
              {!siswa && !error && (
                <div className="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-3">
                  <div className="p-4 bg-slate-50 rounded-full">
                    <School className="w-10 h-10 text-slate-300" />
                  </div>
                  <div className="space-y-1">
                    <p className="font-medium text-slate-600 text-sm">Menunggu Pencarian</p>
                    <p className="text-xs text-slate-400 max-w-xs leading-relaxed">
                      Silakan masukkan NISN Anda secara lengkap untuk menampilkan hasil pengumuman kelulusan.
                    </p>
                  </div>
                </div>
              )}

              {/* Success State */}
              {siswa && !error && (
                <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-300">

                  {/* Graduation Banner & Message */}
                  <div className="flex flex-col items-center text-center p-6 md:p-8 rounded-2xl bg-slate-50 border border-slate-100 shadow-xs space-y-4">

                    {siswa.status_kelulusan === "LULUS" ? (
                      <>
                        <div className="p-3 bg-emerald-100 dark:bg-emerald-950/30 rounded-full animate-bounce">
                          <CheckCircle2 className="w-12 h-12 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <Badge variant="success" className="px-5 py-1.5 text-sm font-bold uppercase tracking-wider rounded-full shadow-xs">
                          {siswa.status_kelulusan}
                        </Badge>
                        <div className="space-y-2">
                          <h3 className="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                            Selamat! Anda dinyatakan LULUS.
                          </h3>
                          <p className="text-xs md:text-sm text-slate-600 max-w-lg leading-relaxed">
                            Kami segenap keluarga besar sekolah mengucapkan selamat atas kelulusan Anda. Teruslah belajar, berkarya, dan raihlah cita-cita setinggi langit!
                          </p>
                        </div>
                      </>
                    ) : (
                      <>
                        <div className="p-3 bg-rose-100 dark:bg-rose-950/30 rounded-full">
                          <XCircle className="w-12 h-12 text-rose-600 dark:text-rose-400" />
                        </div>
                        <Badge variant="danger" className="px-5 py-1.5 text-sm font-bold uppercase tracking-wider rounded-full shadow-xs">
                          {siswa.status_kelulusan}
                        </Badge>
                        <div className="space-y-2">
                          <h3 className="text-lg font-semibold text-rose-800 dark:text-rose-400">
                            Status Kelulusan: Belum Lulus
                          </h3>
                          <p className="text-xs md:text-sm text-slate-600 max-w-lg leading-relaxed">
                            Terima kasih telah mengikuti seluruh proses pendidikan. Silakan menghubungi pihak sekolah untuk informasi lebih lanjut.
                          </p>
                        </div>
                      </>
                    )}

                  </div>

                  {/* Student Details Grid */}
                  <div className="space-y-4">
                    <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                      <User className="w-5 h-5 text-brand-primary" />
                      Data Diri Siswa
                    </h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div className="p-4 bg-slate-50/50 rounded-xl border border-slate-100 flex items-center space-x-3">
                        <div className="p-2 bg-slate-100 rounded-lg text-slate-500">
                          <User className="w-4 h-4" />
                        </div>
                        <div>
                          <p className="text-xs text-slate-400">Nama Siswa</p>
                          <p className="text-sm font-semibold text-slate-800">{siswa.nama_siswa}</p>
                        </div>
                      </div>

                      <div className="p-4 bg-slate-50/50 rounded-xl border border-slate-100 flex items-center space-x-3">
                        <div className="p-2 bg-slate-100 rounded-lg text-slate-500">
                          <Hash className="w-4 h-4" />
                        </div>
                        <div>
                          <p className="text-xs text-slate-400">NISN</p>
                          <p className="text-sm font-semibold text-slate-800">{siswa.nisn}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <Separator />

                  {/* Grades Table */}
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <BookOpen className="w-5 h-5 text-brand-primary" />
                        Daftar Nilai Akhir
                      </h3>
                      <div className="flex items-center space-x-2 bg-brand-soft-bg/20 text-brand-primary px-3.5 py-1.5 rounded-xl border border-brand-soft-bg/30">
                        <TrendingUp className="w-4 h-4" />
                        <span className="text-xs font-semibold">Rata-rata:</span>
                        <span className="text-sm font-bold">{siswa.rata_rata}</span>
                      </div>
                    </div>

                    <div className="overflow-hidden rounded-xl border border-slate-100 shadow-xs">
                      <Table>
                        <TableHeader className="bg-slate-50">
                          <TableRow>
                            <TableHead className="w-16 text-center font-semibold text-slate-700">No</TableHead>
                            <TableHead className="font-semibold text-slate-700">Mata Pelajaran</TableHead>
                            <TableHead className="w-32 text-center font-semibold text-slate-700">Nilai</TableHead>
                          </TableRow>
                        </TableHeader>
                        <TableBody>
                          {siswa.nilai.length > 0 ? (
                            siswa.nilai.map((item, index) => (
                              <TableRow key={index} className="hover:bg-slate-50/40">
                                <TableCell className="text-center font-medium text-slate-500">{index + 1}</TableCell>
                                <TableCell className="font-semibold text-slate-800">{item.mata_pelajaran}</TableCell>
                                <TableCell className="text-center font-bold text-brand-primary">{item.nilai ?? "-"}</TableCell>
                              </TableRow>
                            ))
                          ) : (
                            <TableRow>
                              <TableCell colSpan={3} className="text-center text-slate-400 py-8">
                                Belum ada rincian nilai untuk siswa ini.
                              </TableCell>
                            </TableRow>
                          )}
                        </TableBody>
                      </Table>
                    </div>
                  </div>

                </div>
              )}

            </CardContent>

            {/* Card Footer with school motto / contact info */}
            <CardFooter className="bg-slate-50 p-6 flex flex-col md:flex-row items-center justify-between text-xs text-slate-400 border-t border-slate-100 space-y-2 md:space-y-0 text-center md:text-left">
              <div>
                <p className="font-medium text-slate-600">SMA Negeri Harapan Bangsa</p>
                <p>Jl. Pendidikan No. 45, Jakarta Selatan</p>
              </div>
              <div className="text-slate-400">
                Pusat Data Kelulusan Resmi © 2026. Seluruh hak cipta dilindungi.
              </div>
            </CardFooter>

          </Card>
        </div>
      </div>
    </>
  );
}
