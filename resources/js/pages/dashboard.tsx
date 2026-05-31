import React, { useState, useEffect } from "react";
import { Head, useForm, router } from "@inertiajs/react";
import { 
  School, 
  LayoutDashboard, 
  LogOut, 
  BookOpen, 
  Users, 
  Plus, 
  Edit3, 
  Trash2, 
  Settings, 
  Check, 
  X, 
  UserCheck,
  TrendingUp,
  Award,
  Hash,
  RefreshCw,
  AlertCircle
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from "@/components/ui/table";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import { 
  Dialog, 
  DialogContent, 
  DialogHeader, 
  DialogFooter, 
  DialogTitle, 
  DialogDescription 
} from "@/components/ui/dialog";
import { 
  AlertDialog, 
  AlertDialogContent, 
  AlertDialogHeader, 
  AlertDialogFooter, 
  AlertDialogTitle, 
  AlertDialogDescription, 
  AlertDialogAction, 
  AlertDialogCancel 
} from "@/components/ui/alert-dialog";

// TypeScript Interfaces
export interface Mapel {
  id: number;
  nama_mapel: string;
  created_at?: string;
  updated_at?: string;
}

export interface StudentGradeRelation {
  id: number;
  siswa_id: number;
  mapel_id: number;
  nilai: number | null;
  mapel?: Mapel;
}

export interface TkaGradeRelation {
  id: number;
  siswa_id: number;
  mapel: "Matematika" | "Bahasa Indonesia";
  nilai: number;
}

export interface Student {
  id: number;
  nisn: string;
  nama_siswa: string;
  lulus: boolean;
  nilai: StudentGradeRelation[];
  tka?: TkaGradeRelation[];
  created_at?: string;
  updated_at?: string;
}

export interface DashboardProps {
  mapel: Mapel[];
  siswa: Student[];
}

export default function Dashboard({ mapel, siswa }: DashboardProps) {
  // Active Tab State (default: mapel)
  const [activeTab, setActiveTab] = useState("mapel");

  // --- Mapel Dialog States ---
  const [isMapelOpen, setIsMapelOpen] = useState(false);
  const [selectedMapel, setSelectedMapel] = useState<Mapel | null>(null);

  // --- Siswa Dialog States ---
  const [isSiswaOpen, setIsSiswaOpen] = useState(false);
  const [selectedSiswa, setSelectedSiswa] = useState<Student | null>(null);

  // --- Delete Confirm States ---
  const [isDeleteMapelOpen, setIsDeleteMapelOpen] = useState(false);
  const [mapelToDelete, setMapelToDelete] = useState<Mapel | null>(null);
  
  const [isDeleteSiswaOpen, setIsDeleteSiswaOpen] = useState(false);
  const [siswaToDelete, setSiswaToDelete] = useState<Student | null>(null);

  // --- Atur Nilai Dialog States ---
  const [isNilaiOpen, setIsNilaiOpen] = useState(false);
  const [activeSiswaForNilai, setActiveSiswaForNilai] = useState<Student | null>(null);

  // --- Forms ---
  const mapelForm = useForm({
    nama_mapel: "",
  });

  const siswaForm = useForm({
    nisn: "",
    nama_siswa: "",
    lulus: "1", // Representing boolean true
  });

  const nilaiForm = useForm({
    nilai: [] as { mapel_id: string; nilai: string }[],
    tka: [] as { mapel: "Matematika" | "Bahasa Indonesia"; nilai: string }[],
  });

  // --- Handlers for Mapel CRUD ---
  const openAddMapel = () => {
    mapelForm.reset();
    mapelForm.clearErrors();
    setSelectedMapel(null);
    setIsMapelOpen(true);
  };

  const openEditMapel = (item: Mapel) => {
    mapelForm.setData("nama_mapel", item.nama_mapel);
    mapelForm.clearErrors();
    setSelectedMapel(item);
    setIsMapelOpen(true);
  };

  const handleMapelSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedMapel) {
      mapelForm.put(`/mapel/${selectedMapel.id}`, {
        onSuccess: () => {
          setIsMapelOpen(false);
          mapelForm.reset();
        },
      });
    } else {
      mapelForm.post("/mapel", {
        onSuccess: () => {
          setIsMapelOpen(false);
          mapelForm.reset();
        },
      });
    }
  };

  const confirmDeleteMapel = (item: Mapel) => {
    setMapelToDelete(item);
    setIsDeleteMapelOpen(true);
  };

  const handleDeleteMapel = () => {
    if (!mapelToDelete) return;
    router.delete(`/mapel/${mapelToDelete.id}`, {
      onSuccess: () => {
        setIsDeleteMapelOpen(false);
        setMapelToDelete(null);
      },
    });
  };

  // --- Handlers for Siswa CRUD ---
  const openAddSiswa = () => {
    siswaForm.reset();
    siswaForm.clearErrors();
    setSelectedSiswa(null);
    setIsSiswaOpen(true);
  };

  const openEditSiswa = (item: Student) => {
    siswaForm.setData({
      nisn: item.nisn,
      nama_siswa: item.nama_siswa,
      lulus: item.lulus ? "1" : "0",
    });
    siswaForm.clearErrors();
    setSelectedSiswa(item);
    setIsSiswaOpen(true);
  };

  const handleSiswaSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedSiswa) {
      siswaForm.put(`/siswa/${selectedSiswa.id}`, {
        onSuccess: () => {
          setIsSiswaOpen(false);
          siswaForm.reset();
        },
      });
    } else {
      siswaForm.post("/siswa", {
        onSuccess: () => {
          setIsSiswaOpen(false);
          siswaForm.reset();
        },
      });
    }
  };

  const confirmDeleteSiswa = (item: Student) => {
    setSiswaToDelete(item);
    setIsDeleteSiswaOpen(true);
  };

  const handleDeleteSiswa = () => {
    if (!siswaToDelete) return;
    router.delete(`/siswa/${siswaToDelete.id}`, {
      onSuccess: () => {
        setIsDeleteSiswaOpen(false);
        setSiswaToDelete(null);
      },
    });
  };

  // --- Handlers for Atur Nilai Dialog ---
  const openAturNilai = (item: Student) => {
    const activeGrades = item.nilai.map((grade) => ({
      mapel_id: String(grade.mapel_id),
      nilai: String(grade.nilai ?? ""),
    }));

    const tkaMatematika = item.tka?.find((t) => t.mapel === "Matematika")?.nilai ?? "";
    const tkaBahasaIndo = item.tka?.find((t) => t.mapel === "Bahasa Indonesia")?.nilai ?? "";

    nilaiForm.setData({
      nilai: activeGrades,
      tka: [
        { mapel: "Matematika", nilai: String(tkaMatematika) },
        { mapel: "Bahasa Indonesia", nilai: String(tkaBahasaIndo) },
      ],
    });
    nilaiForm.clearErrors();
    setActiveSiswaForNilai(item);
    setIsNilaiOpen(true);
  };

  const addNilaiRow = () => {
    const currentNilai = [...nilaiForm.data.nilai];
    const available = getAvailableMapels(currentNilai.length);
    const presetMapelId = available.length > 0 ? String(available[0].id) : "";
    nilaiForm.setData("nilai", [
      ...currentNilai,
      { mapel_id: presetMapelId, nilai: "" }
    ]);
  };

  const removeNilaiRow = (indexToRemove: number) => {
    const updated = nilaiForm.data.nilai.filter((_, idx) => idx !== indexToRemove);
    nilaiForm.setData("nilai", updated);
  };

  const updateNilaiRow = (index: number, field: "mapel_id" | "nilai", val: string) => {
    const updated = nilaiForm.data.nilai.map((item, idx) => {
      if (idx === index) {
        return { ...item, [field]: val };
      }
      return item;
    });
    nilaiForm.setData("nilai", updated);
  };

  const handleNilaiSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!activeSiswaForNilai) return;

    nilaiForm.transform((data) => ({
      ...data,
      tka: data.tka.filter((t) => t.nilai !== "" && t.nilai !== null && t.nilai !== undefined),
    }));

    nilaiForm.post(`/siswa/${activeSiswaForNilai.id}/nilai`, {
      onSuccess: () => {
        setIsNilaiOpen(false);
        setActiveSiswaForNilai(null);
      },
    });
  };

  // Real-time select options filter helper
  const getAvailableMapels = (currentRowIndex: number) => {
    const chosenMapelIds = nilaiForm.data.nilai
      .filter((_, idx) => idx !== currentRowIndex)
      .map((item) => Number(item.mapel_id))
      .filter(Boolean);

    return mapel.filter((m) => !chosenMapelIds.includes(m.id));
  };

  const handleLogout = () => {
    router.post("/logout");
  };

  return (
    <>
      <Head>
        <title>Dashboard Admin - Portal Kelulusan</title>
      </Head>

      <div className="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans">
        
        {/* Sidebar Kiri */}
        <aside className="w-full md:w-64 bg-brand-primary text-white flex flex-col shrink-0">
          
          {/* Logo & School Header */}
          <div className="p-6 border-b border-white/10 flex items-center gap-3">
            <div className="p-2 bg-white/10 rounded-lg">
              <School className="w-6 h-6 text-brand-soft-bg" />
            </div>
            <div>
              <h2 className="text-[10px] font-bold tracking-widest text-brand-soft-bg uppercase leading-none">
                Portal Admin
              </h2>
              <h1 className="text-sm font-extrabold tracking-tight mt-1 leading-none">
                SMP ISLAM PARLAUNGAN
              </h1>
            </div>
          </div>

          {/* Navigation Links */}
          <nav className="flex-1 p-4 space-y-1">
            <button
              onClick={() => setActiveTab("mapel")}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-all cursor-pointer ${
                activeTab === "mapel" 
                  ? "bg-white/15 text-white shadow-xs" 
                  : "text-white/70 hover:text-white hover:bg-white/5"
              }`}
            >
              <LayoutDashboard className="w-4 h-4" />
              Dashboard
            </button>
          </nav>

          {/* Logout Button in Sidebar Footer */}
          <div className="p-4 border-t border-white/10">
            <button
              onClick={handleLogout}
              className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-rose-600/90 hover:bg-rose-600 text-white cursor-pointer transition-all shadow-md"
            >
              <LogOut className="w-4 h-4" />
              Keluar Sesi
            </button>
          </div>

        </aside>

        {/* Main Content Area */}
        <main className="flex-1 flex flex-col overflow-hidden min-h-screen">
          
          {/* Main Top Header */}
          <header className="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between shrink-0">
            <div>
              <h1 className="text-xl font-bold text-slate-800">Dashboard Administrator</h1>
              <p className="text-xs text-slate-400">Selamat datang kembali di panel kontrol data sekolah</p>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-xs font-semibold px-2.5 py-1 bg-brand-soft-bg/30 text-brand-primary rounded-full">
                Sesi Aktif
              </span>
            </div>
          </header>

          {/* Core Panel Content */}
          <div className="flex-1 p-6 overflow-y-auto space-y-6">
            
            {/* Tabs & Content */}
            <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
              
              <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-3">
                <TabsList className="bg-slate-100 p-1">
                  <TabsTrigger value="mapel" className="gap-2">
                    <BookOpen className="w-4 h-4" />
                    Mata Pelajaran
                  </TabsTrigger>
                  <TabsTrigger value="siswa" className="gap-2">
                    <Users className="w-4 h-4" />
                    Siswa
                  </TabsTrigger>
                </TabsList>
                
                {/* Dynamically display action button on top-right based on active tab */}
                {activeTab === "mapel" ? (
                  <Button onClick={openAddMapel} className="bg-brand-primary hover:bg-brand-primary/95 text-white gap-1.5 rounded-lg cursor-pointer">
                    <Plus className="w-4 h-4" />
                    Tambah Mata Pelajaran
                  </Button>
                ) : (
                  <Button onClick={openAddSiswa} className="bg-brand-primary hover:bg-brand-primary/95 text-white gap-1.5 rounded-lg cursor-pointer">
                    <Plus className="w-4 h-4" />
                    Tambah Siswa
                  </Button>
                )}
              </div>

              {/* Mata Pelajaran Tab Content */}
              <TabsContent value="mapel">
                <Card className="border-none shadow-xs bg-white rounded-xl overflow-hidden mt-4">
                  <CardHeader className="border-b border-slate-50 px-6 py-4">
                    <CardTitle className="text-base font-bold text-slate-800">Daftar Mata Pelajaran</CardTitle>
                    <CardDescription>Manajemen data mata pelajaran kurikulum kelulusan sekolah.</CardDescription>
                  </CardHeader>
                  
                  <CardContent className="p-0">
                    <Table>
                      <TableHeader className="bg-slate-50/50">
                        <TableRow>
                          <TableHead className="w-20 text-center font-bold text-slate-600">No</TableHead>
                          <TableHead className="font-bold text-slate-600">Nama Mata Pelajaran</TableHead>
                          <TableHead className="w-40 text-center font-bold text-slate-600">Aksi</TableHead>
                        </TableRow>
                      </TableHeader>
                      
                      <TableBody>
                        {mapel.length > 0 ? (
                          mapel.map((item, index) => (
                            <TableRow key={item.id} className="hover:bg-slate-50/40">
                              <TableCell className="text-center font-medium text-slate-500">{index + 1}</TableCell>
                              <TableCell className="font-semibold text-slate-800">{item.nama_mapel}</TableCell>
                              <TableCell className="text-center">
                                <div className="flex items-center justify-center gap-1.5">
                                  <Button 
                                    size="sm" 
                                    variant="outline" 
                                    onClick={() => openEditMapel(item)}
                                    className="h-8 w-8 p-0 text-slate-500 hover:text-brand-primary border-slate-200 cursor-pointer"
                                    title="Edit Mapel"
                                  >
                                    <Edit3 className="w-3.5 h-3.5" />
                                  </Button>
                                  <Button 
                                    size="sm" 
                                    variant="destructive"
                                    onClick={() => confirmDeleteMapel(item)}
                                    className="h-8 w-8 p-0 bg-rose-50 text-rose-600 hover:bg-rose-100 border-none cursor-pointer"
                                    title="Hapus Mapel"
                                  >
                                    <Trash2 className="w-3.5 h-3.5" />
                                  </Button>
                                </div>
                              </TableCell>
                            </TableRow>
                          ))
                        ) : (
                          <TableRow>
                            <TableCell colSpan={3} className="text-center text-slate-400 py-8">
                              Belum ada mata pelajaran. Silakan tambahkan baru.
                            </TableCell>
                          </TableRow>
                        )}
                      </TableBody>
                    </Table>
                  </CardContent>
                </Card>
              </TabsContent>

              {/* Siswa Tab Content */}
              <TabsContent value="siswa">
                <Card className="border-none shadow-xs bg-white rounded-xl overflow-hidden mt-4">
                  <CardHeader className="border-b border-slate-50 px-6 py-4">
                    <CardTitle className="text-base font-bold text-slate-800">Daftar Siswa</CardTitle>
                    <CardDescription>Manajemen data peserta didik, status kelulusan, dan nilai mata pelajaran.</CardDescription>
                  </CardHeader>

                  <CardContent className="p-0">
                    <Table>
                      <TableHeader className="bg-slate-50/50">
                        <TableRow>
                          <TableHead className="w-20 text-center font-bold text-slate-600">No</TableHead>
                          <TableHead className="w-48 font-bold text-slate-600">NISN</TableHead>
                          <TableHead className="font-bold text-slate-600">Nama Siswa</TableHead>
                          <TableHead className="w-44 text-center font-bold text-slate-600">Status Kelulusan</TableHead>
                          <TableHead className="w-64 text-center font-bold text-slate-600">Aksi</TableHead>
                        </TableRow>
                      </TableHeader>

                      <TableBody>
                        {siswa.length > 0 ? (
                          siswa.map((item, index) => (
                            <TableRow key={item.id} className="hover:bg-slate-50/40">
                              <TableCell className="text-center font-medium text-slate-500">{index + 1}</TableCell>
                              <TableCell className="font-mono text-sm font-semibold text-slate-700">{item.nisn}</TableCell>
                              <TableCell className="font-semibold text-slate-800">{item.nama_siswa}</TableCell>
                              <TableCell className="text-center">
                                <Badge variant={item.lulus ? "success" : "danger"} className="rounded-full font-bold">
                                  {item.lulus ? "LULUS" : "TIDAK LULUS"}
                                </Badge>
                              </TableCell>
                              <TableCell className="text-center">
                                <div className="flex items-center justify-center gap-1.5">
                                  <Button 
                                    size="sm"
                                    onClick={() => openAturNilai(item)}
                                    className="bg-brand-secondary hover:bg-brand-secondary/90 text-white font-semibold text-xs px-3.5 h-8 gap-1 rounded-lg cursor-pointer"
                                    title="Atur Rincian Nilai"
                                  >
                                    <Settings className="w-3.5 h-3.5" />
                                    Atur Nilai
                                  </Button>
                                  <Button 
                                    size="sm" 
                                    variant="outline" 
                                    onClick={() => openEditSiswa(item)}
                                    className="h-8 w-8 p-0 text-slate-500 hover:text-brand-primary border-slate-200 cursor-pointer"
                                    title="Edit Biodata"
                                  >
                                    <Edit3 className="w-3.5 h-3.5" />
                                  </Button>
                                  <Button 
                                    size="sm" 
                                    variant="destructive"
                                    onClick={() => confirmDeleteSiswa(item)}
                                    className="h-8 w-8 p-0 bg-rose-50 text-rose-600 hover:bg-rose-100 border-none cursor-pointer"
                                    title="Hapus Siswa"
                                  >
                                    <Trash2 className="w-3.5 h-3.5" />
                                  </Button>
                                </div>
                              </TableCell>
                            </TableRow>
                          ))
                        ) : (
                          <TableRow>
                            <TableCell colSpan={5} className="text-center text-slate-400 py-8">
                              Belum ada data siswa. Silakan tambahkan baru.
                            </TableCell>
                          </TableRow>
                        )}
                      </TableBody>
                    </Table>
                  </CardContent>
                </Card>
              </TabsContent>

            </Tabs>
          </div>
        </main>
      </div>

      {/* --- DIALOGS & ALERTS --- */}

      {/* 1. Dialog Tambah / Edit Mata Pelajaran */}
      <Dialog open={isMapelOpen} onOpenChange={setIsMapelOpen}>
        <DialogContent className="sm:max-w-[450px]">
          <DialogHeader>
            <DialogTitle>{selectedMapel ? "Edit Mata Pelajaran" : "Tambah Mata Pelajaran"}</DialogTitle>
            <DialogDescription>
              Silakan isi nama mata pelajaran secara unik untuk kurikulum kelulusan.
            </DialogDescription>
          </DialogHeader>

          <form onSubmit={handleMapelSubmit} className="space-y-4 py-2">
            <div className="space-y-1.5">
              <Label htmlFor="nama_mapel" className="font-semibold text-slate-700">Nama Mata Pelajaran</Label>
              <Input
                id="nama_mapel"
                required
                value={mapelForm.data.nama_mapel}
                onChange={(e) => mapelForm.setData("nama_mapel", e.target.value)}
                placeholder="Contoh: Matematika, Fisika"
                disabled={mapelForm.processing}
                className="focus-visible:ring-brand-primary"
              />
              {mapelForm.errors.nama_mapel && (
                <p className="text-xs text-rose-500 font-medium mt-1">{mapelForm.errors.nama_mapel}</p>
              )}
            </div>

            <DialogFooter className="pt-4 border-t border-slate-50">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => setIsMapelOpen(false)}
                className="cursor-pointer"
              >
                Batal
              </Button>
              <Button 
                type="submit" 
                disabled={mapelForm.processing}
                className="bg-brand-primary hover:bg-brand-primary/95 text-white cursor-pointer gap-1.5"
              >
                {mapelForm.processing && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                Simpan
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* 2. Dialog Tambah / Edit Siswa */}
      <Dialog open={isSiswaOpen} onOpenChange={setIsSiswaOpen}>
        <DialogContent className="sm:max-w-[450px]">
          <DialogHeader>
            <DialogTitle>{selectedSiswa ? "Edit Data Siswa" : "Tambah Data Siswa"}</DialogTitle>
            <DialogDescription>
              Silakan isi biodata siswa secara lengkap untuk akses portal kelulusan.
            </DialogDescription>
          </DialogHeader>

          <form onSubmit={handleSiswaSubmit} className="space-y-4 py-2">
            <div className="space-y-1.5">
              <Label htmlFor="nisn" className="font-semibold text-slate-700">NISN (10 Digit)</Label>
              <Input
                id="nisn"
                required
                value={siswaForm.data.nisn}
                onChange={(e) => siswaForm.setData("nisn", e.target.value)}
                placeholder="Masukkan 10 digit NISN..."
                disabled={siswaForm.processing}
                className="focus-visible:ring-brand-primary"
              />
              {siswaForm.errors.nisn && (
                <p className="text-xs text-rose-500 font-medium mt-1">{siswaForm.errors.nisn}</p>
              )}
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="nama_siswa" className="font-semibold text-slate-700">Nama Siswa</Label>
              <Input
                id="nama_siswa"
                required
                value={siswaForm.data.nama_siswa}
                onChange={(e) => siswaForm.setData("nama_siswa", e.target.value)}
                placeholder="Masukkan nama lengkap siswa..."
                disabled={siswaForm.processing}
                className="focus-visible:ring-brand-primary"
              />
              {siswaForm.errors.nama_siswa && (
                <p className="text-xs text-rose-500 font-medium mt-1">{siswaForm.errors.nama_siswa}</p>
              )}
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="lulus" className="font-semibold text-slate-700">Status Kelulusan</Label>
              <Select
                id="lulus"
                value={siswaForm.data.lulus}
                onChange={(e) => siswaForm.setData("lulus", e.target.value)}
                disabled={siswaForm.processing}
                className="focus-visible:ring-brand-primary"
              >
                <option value="1">LULUS</option>
                <option value="0">TIDAK LULUS</option>
              </Select>
              {siswaForm.errors.lulus && (
                <p className="text-xs text-rose-500 font-medium mt-1">{siswaForm.errors.lulus}</p>
              )}
            </div>

            <DialogFooter className="pt-4 border-t border-slate-50">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => setIsSiswaOpen(false)}
                className="cursor-pointer"
              >
                Batal
              </Button>
              <Button 
                type="submit" 
                disabled={siswaForm.processing}
                className="bg-brand-primary hover:bg-brand-primary/95 text-white cursor-pointer gap-1.5"
              >
                {siswaForm.processing && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                Simpan
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* 3. AlertDialog Konfirmasi Hapus Mapel */}
      <AlertDialog open={isDeleteMapelOpen} onOpenChange={setIsDeleteMapelOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
            <AlertDialogDescription>
              Yakin ingin menghapus mata pelajaran ini? Tindakan ini akan menghapus nilai siswa yang berkaitan dengan mata pelajaran ini secara permanen.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel className="cursor-pointer">Batal</AlertDialogCancel>
            <AlertDialogAction onClick={handleDeleteMapel} className="cursor-pointer bg-rose-600 hover:bg-rose-700 text-white">
              Hapus
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* 4. AlertDialog Konfirmasi Hapus Siswa */}
      <AlertDialog open={isDeleteSiswaOpen} onOpenChange={setIsDeleteSiswaOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
            <AlertDialogDescription>
              Yakin ingin menghapus siswa ini? Rincian nilai dan seluruh data terkait akan dihapus secara permanen dari server database.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel className="cursor-pointer">Batal</AlertDialogCancel>
            <AlertDialogAction onClick={handleDeleteSiswa} className="cursor-pointer bg-rose-600 hover:bg-rose-700 text-white">
              Hapus
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* 5. Dialog Atur Nilai Siswa (Ukuran Besar) */}
      <Dialog open={isNilaiOpen} onOpenChange={setIsNilaiOpen}>
        <DialogContent className="sm:max-w-[700px] max-h-[85vh] flex flex-col p-6 overflow-hidden">
          <DialogHeader className="shrink-0">
            <DialogTitle>Atur Nilai Akhir Siswa</DialogTitle>
            <DialogDescription>
              Sesuaikan daftar mata pelajaran dan nilai akhir untuk siswa yang bersangkutan.
            </DialogDescription>
          </DialogHeader>

          {/* Student Banner Info */}
          {activeSiswaForNilai && (
            <div className="shrink-0 bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 mt-2">
              <div className="flex items-center gap-2">
                <UserCheck className="w-5 h-5 text-brand-primary" />
                <div>
                  <p className="text-xs text-slate-400">Nama Siswa</p>
                  <p className="text-sm font-bold text-slate-800">{activeSiswaForNilai.nama_siswa}</p>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <Hash className="w-5 h-5 text-brand-secondary" />
                <div>
                  <p className="text-xs text-slate-400">NISN</p>
                  <p className="text-sm font-mono font-bold text-slate-800">{activeSiswaForNilai.nisn}</p>
                </div>
              </div>
            </div>
          )}

          {/* Dynamic Row Form Container */}
          <form onSubmit={handleNilaiSubmit} className="flex-1 flex flex-col overflow-hidden pt-4 space-y-4">
            
            {/* Display General Validation Errors (like duplicate distinct validation) */}
            {Object.keys(nilaiForm.errors).some(k => k.startsWith('nilai')) && (
              <div className="shrink-0 bg-rose-50/50 border border-rose-100 rounded-xl p-3 flex items-start gap-2">
                <AlertCircle className="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
                <div>
                  <p className="text-xs font-semibold text-rose-800">Terdapat kesalahan pengisian nilai:</p>
                  <ul className="list-disc list-inside text-[11px] text-rose-700 mt-1 space-y-0.5">
                    {Object.entries(nilaiForm.errors).map(([key, msg]) => (
                      <li key={key}>{msg}</li>
                    ))}
                  </ul>
                </div>
              </div>
            )}

            {/* Header for dynamic row table */}
            <div className="shrink-0 flex items-center justify-between px-2">
              <span className="text-xs font-bold text-slate-500">Rincian Nilai Mata Pelajaran</span>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addNilaiRow}
                disabled={mapel.length === 0 || nilaiForm.data.nilai.length >= mapel.length}
                className="h-8 gap-1 bg-white border-slate-200 text-brand-primary hover:text-brand-primary/90 font-bold cursor-pointer disabled:opacity-50"
              >
                <Plus className="w-3.5 h-3.5" />
                Tambah Baris
              </Button>
            </div>

            {/* Scrollable rows content */}
            <div className="flex-1 overflow-y-auto pr-1 border border-slate-100 rounded-xl p-2 bg-slate-50/20 space-y-2">
              {nilaiForm.data.nilai.length > 0 ? (
                nilaiForm.data.nilai.map((row, index) => {
                  const availableMapels = getAvailableMapels(index);
                  // Find currently selected mapel (in case it is already selected, it should be listed in options of this row)
                  const currentSelectedMapel = mapel.find(m => String(m.id) === row.mapel_id);
                  const optionsList = currentSelectedMapel 
                    ? [currentSelectedMapel, ...availableMapels] 
                    : availableMapels;

                  return (
                    <div 
                      key={index} 
                      className="flex items-center gap-3 p-3 bg-white border border-slate-100 rounded-xl shadow-2xs animate-in fade-in-0 duration-150"
                    >
                      {/* Column 1: Mapel Select */}
                      <div className="flex-1">
                        <Select
                          required
                          value={row.mapel_id}
                          onChange={(e) => updateNilaiRow(index, "mapel_id", e.target.value)}
                          className="h-9 font-semibold text-slate-800"
                        >
                          <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                          {optionsList.map((m) => (
                            <option key={m.id} value={String(m.id)}>
                              {m.nama_mapel}
                            </option>
                          ))}
                        </Select>
                      </div>

                      {/* Column 2: Nilai Input */}
                      <div className="w-28 shrink-0">
                        <Input
                          required
                          type="number"
                          min="0"
                          max="100"
                          placeholder="Nilai (0-100)"
                          value={row.nilai}
                          onChange={(e) => updateNilaiRow(index, "nilai", e.target.value)}
                          className="h-9 text-center font-bold text-brand-primary placeholder:text-slate-300"
                        />
                      </div>

                      {/* Column 3: Delete Row Button */}
                      <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        onClick={() => removeNilaiRow(index)}
                        className="h-9 w-9 p-0 bg-rose-50 hover:bg-rose-100 text-rose-600 border-none cursor-pointer shrink-0"
                        title="Hapus Baris"
                      >
                        <X className="w-4 h-4" />
                      </Button>
                    </div>
                  );
                })
              ) : (
                <div className="h-full flex flex-col items-center justify-center text-center text-slate-400 py-12 space-y-2">
                  <BookOpen className="w-8 h-8 text-slate-300 animate-pulse" />
                  <p className="text-xs font-semibold text-slate-600">Belum Ada Nilai Ditambahkan</p>
                  <p className="text-[10px] text-slate-400 max-w-xs leading-relaxed">
                    Klik tombol "Tambah Baris" di atas untuk mulai menginput data nilai mata pelajaran siswa.
                  </p>
                </div>
              )}
            </div>

            {/* TKA Section */}
            <div className="shrink-0 border-t border-slate-100 pt-4 px-2">
              <span className="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                <Award className="w-4 h-4 text-brand-secondary" />
                Nilai Tes Kemampuan Akademik (TKA)
              </span>
            </div>

            <div className="shrink-0 grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
              <div className="space-y-1.5">
                <Label htmlFor="tka_matematika" className="font-semibold text-slate-700">TKA Matematika</Label>
                <Input
                  id="tka_matematika"
                  type="number"
                  min="0"
                  max="100"
                  placeholder="Nilai Matematika (0-100)"
                  value={nilaiForm.data.tka[0]?.nilai ?? ""}
                  onChange={(e) => {
                    const updatedTka = [...nilaiForm.data.tka];
                    if (updatedTka[0]) {
                      updatedTka[0].nilai = e.target.value;
                      nilaiForm.setData("tka", updatedTka);
                    }
                  }}
                  className="focus-visible:ring-brand-secondary text-slate-700 font-bold bg-white"
                />
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="tka_bahasa_indo" className="font-semibold text-slate-700">TKA Bahasa Indonesia</Label>
                <Input
                  id="tka_bahasa_indo"
                  type="number"
                  min="0"
                  max="100"
                  placeholder="Nilai B. Indonesia (0-100)"
                  value={nilaiForm.data.tka[1]?.nilai ?? ""}
                  onChange={(e) => {
                    const updatedTka = [...nilaiForm.data.tka];
                    if (updatedTka[1]) {
                      updatedTka[1].nilai = e.target.value;
                      nilaiForm.setData("tka", updatedTka);
                    }
                  }}
                  className="focus-visible:ring-brand-secondary text-slate-700 font-bold bg-white"
                />
              </div>
            </div>

            {/* Footer buttons */}
            <DialogFooter className="shrink-0 pt-4 border-t border-slate-100">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => setIsNilaiOpen(false)}
                className="cursor-pointer"
              >
                Batal
              </Button>
              <Button 
                type="submit" 
                disabled={nilaiForm.processing}
                className="bg-brand-primary hover:bg-brand-primary/95 text-white cursor-pointer gap-1.5"
              >
                {nilaiForm.processing && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                Simpan Rincian Nilai
              </Button>
            </DialogFooter>

          </form>
        </DialogContent>
      </Dialog>
    </>
  );
}
